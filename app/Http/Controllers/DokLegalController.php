<?php

namespace App\Http\Controllers;

use App\Models\DokLegal;
use App\Models\Perusahaan;
use App\Models\KategoriDok;
use App\Models\JenisDok;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use DateInterval;
use DatePeriod;
use DateTime;
use RealRashid\SweetAlert\Facades\Alert;

class DokLegalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Ambil semua data untuk DataTables (tanpa paginasi)
        $dokLegals = DokLegal::with(['perusahaan', 'kategori', 'jenis'])->latest()->get();

        // Ambil daftar kategori dan jenis dokumen untuk filter dropdown
        $kategoris = KategoriDok::select('KategoriDok')->distinct()->pluck('KategoriDok');
        $jenisDoks = JenisDok::select('JenisDok')->distinct()->pluck('JenisDok');

        // Ambil daftar perusahaan untuk filter dropdown
        $perusahaans = Perusahaan::orderBy('NamaPrsh')->pluck('NamaPrsh', 'id');

        // Ambil jenis masa berlaku untuk filter dropdown
        $jenisMasaBerlaku = ['Tetap', 'Perpanjangan'];

        // check if user is admin
        $isAdmin = auth()->user()->isAdmin();

        // If user is admin, grant all permissions
        if ($isAdmin) {
            $hasViewPermission = true;
            $hasEditPermission = true;
            $hasDeletePermission = true;
            $hasDownloadPermission = true;
            $hasCreatePermission = true;
        } else {
            // Individual permission checks for non-admin users
            $hasViewPermission = auth()->user()->hasAccess('dokLegal', 'detail');
            $hasEditPermission = auth()->user()->hasAccess('dokLegal', 'ubah');
            $hasDeletePermission = auth()->user()->hasAccess('dokLegal', 'hapus');
            $hasDownloadPermission = auth()->user()->hasAccess('dokLegal', 'download');
            $hasCreatePermission = auth()->user()->hasAccess('dokLegal', 'tambah');
        }

        return view('dokLegal.index', compact(
            'dokLegals',
            'kategoris',
            'jenisDoks',
            'jenisMasaBerlaku',
            'perusahaans',
            'hasViewPermission',
            'hasEditPermission',
            'hasDeletePermission',
            'hasDownloadPermission',
            'hasCreatePermission',
            'isAdmin'
        ));
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Generate ID Kode otomatis
        $idKode = DokLegal::generateIdKode();

        // Data master untuk dropdown
        $perusahaans = Perusahaan::orderBy('NamaPrsh')->pluck('NamaPrsh', 'id');
        $kategoris = KategoriDok::orderBy('KategoriDok')->pluck('KategoriDok', 'id');
        $jenisDoks = JenisDok::orderBy('JenisDok')->pluck('JenisDok', 'id');

        return view('dokLegal.create', compact('idKode', 'perusahaans', 'kategoris', 'jenisDoks'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Definisi validasi
        $rules = [
            'IdKode' => 'required|string|max:255|unique:B01DokLegal,IdKode',
            'NoRegDok' => [
                'required',
                'string',
                'max:50',
                'unique:B01DokLegal,NoRegDok'
            ],
            'DokPerusahaan' => 'required|string|max:255',
            'perusahaan_id' => 'required|exists:A03DmPerusahaan,id',
            'KategoriDok' => 'required|string|max:255',
            'kategori_id' => 'required|exists:A04DmKategoriDok,id',
            'JenisDok' => 'required|string|max:255',
            'jenis_id' => 'required|exists:A05DmJenisDok,id',
            'PeruntukanDok' => 'required|string|min:3|max:255',
            'DokAtasNama' => 'required|string|max:255',
            'KetDok' => 'nullable|string',
            'JnsMasaBerlaku' => 'required|in:Tetap,Perpanjangan',
            'TglTerbitDok' => 'required|date|before_or_equal:today',
            'file_dokumen' => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx', // Maksimal 10MB, wajib diisi
            'StsBerlakuDok' => 'required|in:Berlaku,Tidak Berlaku',
        ];

        // Tambahkan validasi untuk Tanggal Berakhir jika Jenis Masa Berlaku adalah Perpanjangan
        if ($request->JnsMasaBerlaku == 'Perpanjangan') {
            $rules['TglBerakhirDok'] = 'required|date|after:TglTerbitDok';
            $rules['TglPengingat'] = 'nullable|date|before:TglBerakhirDok';
        } else {
            $rules['TglBerakhirDok'] = 'nullable|date';
            $rules['TglPengingat'] = 'nullable|date';
        }

        // Pesan validasi kustom
        $messages = [
            'NoRegDok.required' => 'Nomor Register Dokumen wajib diisi',
            'NoRegDok.regex' => 'Format Nomor Register Dokumen tidak valid (gunakan huruf kapital, angka, /, -)',
            'NoRegDok.unique' => 'Nomor Register Dokumen sudah digunakan',
            'perusahaan_id.required' => 'Perusahaan wajib dipilih',
            'perusahaan_id.exists' => 'Perusahaan yang dipilih tidak valid',
            'kategori_id.required' => 'Kategori Dokumen wajib dipilih',
            'kategori_id.exists' => 'Kategori yang dipilih tidak valid',
            'jenis_id.required' => 'Jenis Dokumen wajib dipilih',
            'jenis_id.exists' => 'Jenis yang dipilih tidak valid',
            'PeruntukanDok.required' => 'Peruntukan Dokumen wajib diisi',
            'PeruntukanDok.min' => 'Peruntukan Dokumen minimal 3 karakter',
            'DokAtasNama.required' => 'Atas Nama wajib diisi',
            'JnsMasaBerlaku.required' => 'Jenis Masa Berlaku wajib dipilih',
            'JnsMasaBerlaku.in' => 'Jenis Masa Berlaku tidak valid',
            'TglTerbitDok.required' => 'Tanggal Terbit Dokumen wajib diisi',
            'TglTerbitDok.before_or_equal' => 'Tanggal Terbit tidak boleh di masa depan',
            'TglBerakhirDok.required' => 'Tanggal Berakhir wajib diisi untuk jenis masa berlaku Perpanjangan',
            'TglBerakhirDok.after' => 'Tanggal Berakhir harus setelah Tanggal Terbit',
            'TglPengingat.before' => 'Tanggal Pengingat harus sebelum Tanggal Berakhir',
            'file_dokumen.required' => 'File Dokumen wajib diunggah',
            'file_dokumen.mimes' => 'Format file tidak didukung. Gunakan PDF, JPG, PNG, DOC, DOCX, XLS, atau XLSX',
            'file_dokumen.max' => 'Ukuran file terlalu besar (maksimum 10MB)',
            'StsBerlakuDok.required' => 'Status Berlaku Dokumen wajib dipilih',
            'StsBerlakuDok.in' => 'Status Berlaku Dokumen tidak valid',
        ];

        // Validasi request
        $validated = $request->validate($rules, $messages);

        // Hitung masa berlaku secara otomatis
        if ($request->filled('TglBerakhirDok') && $request->JnsMasaBerlaku == 'Perpanjangan') {
            $tglTerbit = Carbon::parse($request->TglTerbitDok);
            $tglBerakhir = Carbon::parse($request->TglBerakhirDok);

            $validated['MasaBerlaku'] = DokLegal::hitungMasaBerlaku($tglTerbit, $tglBerakhir);
        } else {
            $validated['MasaBerlaku'] = 'Tetap';
        }

        // Hitung masa pengingat secara otomatis
        if ($request->filled('TglPengingat') && $request->filled('TglBerakhirDok')) {
            $tglBerakhir = Carbon::parse($request->TglBerakhirDok);
            $tglPengingat = Carbon::parse($request->TglPengingat);

            $validated['MasaPengingat'] = DokLegal::hitungMasaBerlaku($tglPengingat, $tglBerakhir);
        } else {
            $validated['MasaPengingat'] = '-';
        }

        // Handle file upload
        if ($request->hasFile('file_dokumen')) {
            $file = $request->file('file_dokumen');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('uploads/dokumen', $fileName, 'public');
            $validated['FileDok'] = $fileName;
        }

        // Ambil data master berdasarkan ID
        $perusahaan = Perusahaan::findOrFail($request->perusahaan_id);
        $kategori = KategoriDok::findOrFail($request->kategori_id);
        $jenis = JenisDok::findOrFail($request->jenis_id);

        // Set nilai dari master data
        $validated['DokPerusahaan'] = $perusahaan->NamaPrsh;
        $validated['KategoriDok'] = $kategori->KategoriDok;
        $validated['JenisDok'] = $jenis->JenisDok;

        // Tambahkan informasi user yang membuat dan mengupdate data
        $validated['created_by'] = auth()->user()->id; // Use the actual 'id' property

        DokLegal::create($validated);

        Alert::success('Berhasil', 'Data Dokumen Legal Berhasil Ditambahkan.');
        return redirect()->route('dokLegal.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(DokLegal $dokLegal)
    {
        // Load relations
        $dokLegal->load(['perusahaan', 'kategori', 'jenis']);

        return view('dokLegal.show', compact('dokLegal'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DokLegal $dokLegal)
    {
        // Data master untuk dropdown
        $perusahaans = Perusahaan::orderBy('NamaPrsh')->pluck('NamaPrsh', 'id');
        $kategoris = KategoriDok::orderBy('KategoriDok')->pluck('KategoriDok', 'id');
        $jenisDoks = JenisDok::orderBy('JenisDok')->pluck('JenisDok', 'id');

        return view('dokLegal.edit', compact('dokLegal', 'perusahaans', 'kategoris', 'jenisDoks'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DokLegal $dokLegal)
    {
        // Definisi validasi
        $rules = [
            'IdKode' => [
                'required',
                'string',
                'max:255',
                Rule::unique('B01DokLegal', 'IdKode')->ignore($dokLegal->id)
            ],
            'NoRegDok' => [
                'required',
                'string',
                'max:50', // Hanya huruf kapital, angka, garis miring, dan strip
                Rule::unique('B01DokLegal', 'NoRegDok')->ignore($dokLegal->id)
            ],
            'DokPerusahaan' => 'required|string|max:255',
            'perusahaan_id' => 'required|exists:A03DmPerusahaan,id',
            'KategoriDok' => 'required|string|max:255',
            'kategori_id' => 'required|exists:A04DmKategoriDok,id',
            'JenisDok' => 'required|string|max:255',
            'jenis_id' => 'required|exists:A05DmJenisDok,id',
            'PeruntukanDok' => 'required|string|min:3|max:255',
            'DokAtasNama' => 'required|string|max:255',
            'KetDok' => 'nullable|string',
            'JnsMasaBerlaku' => 'required|in:Tetap,Perpanjangan',
            'TglTerbitDok' => 'required|date|before_or_equal:today',
            'file_dokumen' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx', // Maksimal 10MB
            'StsBerlakuDok' => 'required|in:Berlaku,Tidak Berlaku',
        ];

        // Tambahkan validasi untuk Tanggal Berakhir jika Jenis Masa Berlaku adalah Perpanjangan
        if ($request->JnsMasaBerlaku == 'Perpanjangan') {
            $rules['TglBerakhirDok'] = 'required|date|after:TglTerbitDok';
            $rules['TglPengingat'] = 'nullable|date|before:TglBerakhirDok';
        } else {
            $rules['TglBerakhirDok'] = 'nullable|date';
            $rules['TglPengingat'] = 'nullable|date';
        }

        // Pesan validasi kustom
        $messages = [
            'NoRegDok.required' => 'Nomor Register Dokumen wajib diisi',
            'NoRegDok.regex' => 'Format Nomor Register Dokumen tidak valid (gunakan huruf kapital, angka, /, -)',
            'NoRegDok.unique' => 'Nomor Register Dokumen sudah digunakan',
            'perusahaan_id.required' => 'Perusahaan wajib dipilih',
            'perusahaan_id.exists' => 'Perusahaan yang dipilih tidak valid',
            'kategori_id.required' => 'Kategori Dokumen wajib dipilih',
            'kategori_id.exists' => 'Kategori yang dipilih tidak valid',
            'jenis_id.required' => 'Jenis Dokumen wajib dipilih',
            'jenis_id.exists' => 'Jenis yang dipilih tidak valid',
            'PeruntukanDok.required' => 'Peruntukan Dokumen wajib diisi',
            'PeruntukanDok.min' => 'Peruntukan Dokumen minimal 3 karakter',
            'DokAtasNama.required' => 'Atas Nama wajib diisi',
            'JnsMasaBerlaku.required' => 'Jenis Masa Berlaku wajib dipilih',
            'JnsMasaBerlaku.in' => 'Jenis Masa Berlaku tidak valid',
            'TglTerbitDok.required' => 'Tanggal Terbit Dokumen wajib diisi',
            'TglTerbitDok.before_or_equal' => 'Tanggal Terbit tidak boleh di masa depan',
            'TglBerakhirDok.required' => 'Tanggal Berakhir wajib diisi untuk jenis masa berlaku Perpanjangan',
            'TglBerakhirDok.after' => 'Tanggal Berakhir harus setelah Tanggal Terbit',
            'TglPengingat.before' => 'Tanggal Pengingat harus sebelum Tanggal Berakhir',
            'file_dokumen.mimes' => 'Format file tidak didukung. Gunakan PDF, JPG, PNG, DOC, DOCX, XLS, atau XLSX',
            'file_dokumen.max' => 'Ukuran file terlalu besar (maksimum 10MB)',
            'StsBerlakuDok.required' => 'Status Berlaku Dokumen wajib dipilih',
            'StsBerlakuDok.in' => 'Status Berlaku Dokumen tidak valid',
        ];

        // Validasi request
        $validated = $request->validate($rules, $messages);

        // Hitung masa berlaku secara otomatis
        if ($request->filled('TglBerakhirDok') && $request->JnsMasaBerlaku == 'Perpanjangan') {
            $tglTerbit = Carbon::parse($request->TglTerbitDok);
            $tglBerakhir = Carbon::parse($request->TglBerakhirDok);

            $validated['MasaBerlaku'] = DokLegal::hitungMasaBerlaku($tglTerbit, $tglBerakhir);
        } else {
            $validated['MasaBerlaku'] = 'Tetap';
        }

        // Hitung masa pengingat secara otomatis
        if ($request->filled('TglPengingat') && $request->filled('TglBerakhirDok')) {
            $tglBerakhir = Carbon::parse($request->TglBerakhirDok);
            $tglPengingat = Carbon::parse($request->TglPengingat);

            $validated['MasaPengingat'] = DokLegal::hitungMasaBerlaku($tglPengingat, $tglBerakhir);
        } else {
            $validated['MasaPengingat'] = '-';
        }

        // Handle file upload
        if ($request->hasFile('file_dokumen')) {
            // Delete old file if exists
            if ($dokLegal->FileDok && Storage::disk('public')->exists('uploads/dokumen/' . $dokLegal->FileDok)) {
                Storage::disk('public')->delete('uploads/dokumen/' . $dokLegal->FileDok);
            }

            $file = $request->file('file_dokumen');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('uploads/dokumen', $fileName, 'public');
            $validated['FileDok'] = $fileName;
        }

        // Ambil data master berdasarkan ID
        $perusahaan = Perusahaan::findOrFail($request->perusahaan_id);
        $kategori = KategoriDok::findOrFail($request->kategori_id);
        $jenis = JenisDok::findOrFail($request->jenis_id);

        // Set nilai dari master data
        $validated['DokPerusahaan'] = $perusahaan->NamaPrsh;
        $validated['KategoriDok'] = $kategori->KategoriDok;
        $validated['JenisDok'] = $jenis->JenisDok;

        // Update informasi user yang mengupdate data
        $validated['updated_by'] = auth()->user()->id;

        $dokLegal->update($validated);

        Alert::success('Berhasil', 'Data Dokumen Legal Berhasil Diperbarui.');
        return redirect()->route('dokLegal.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DokLegal $dokLegal)
    {
        // Verifikasi hak akses terlebih dahulu
        if (!auth()->user()->hasAccess('dokLegal', 'HapusAcs')) {
            // Log percobaan akses tidak sah
            \Log::warning('Upaya penghapusan dokumen tanpa izin oleh user: ' . auth()->user()->id . ' untuk dokumen ID: ' . $dokLegal->id);

            // Redirect dengan pesan error
            return redirect()->route('dokLegal.index')
                ->with('error', 'Anda tidak memiliki hak akses untuk menghapus dokumen.');
        }

        try {
            // Delete file if exists
            if ($dokLegal->FileDok && Storage::disk('public')->exists('uploads/dokumen/' . $dokLegal->FileDok)) {
                Storage::disk('public')->delete('uploads/dokumen/' . $dokLegal->FileDok);
            }

            $dokLegal->delete();

            Alert::success('Berhasil', 'Data Dokumen Legal Berhasil Dihapus.');
            return redirect()->route('dokLegal.index');
        } catch (\Exception $e) {
            \Log::error('Error saat menghapus dokumen: ' . $e->getMessage());
            return redirect()->route('dokLegal.index')
                ->with('error', 'Terjadi kesalahan saat menghapus dokumen. Silakan coba lagi.');
        }
    }


    public function download(DokLegal $dokLegal)
    {
        if (!$dokLegal->FileDok || !Storage::disk('public')->exists('uploads/dokumen/' . $dokLegal->FileDok)) {
            return back()->with('error', 'File tidak ditemukan.');
        }

        // Mendapatkan ekstensi dari file asli
        $originalExtension = pathinfo($dokLegal->FileDok, PATHINFO_EXTENSION);

        // Format tanggal terbit untuk nama file
        $tanggalTerbit = $dokLegal->TglTerbitDok ? $dokLegal->TglTerbitDok->format('Ymd') : date('Ymd');

        // Membersihkan string dari karakter yang tidak valid untuk nama file (Windows & Linux)
        // Karakter tidak valid: / \ : * ? " < > |
        $sanitizeFileName = function ($string) {
            // Pertama ganti semua karakter tidak valid dengan tanda strip
            $cleaned = preg_replace('/[\/\\\:*?"<>|]/', '-', $string);
            // Hilangkan kemungkinan tanda strip berlebih
            $cleaned = preg_replace('/-+/', '-', $cleaned);
            // Trim tanda strip di awal dan akhir
            return trim($cleaned, '-');
        };

        // Membersihkan dan memformat komponen nama file
        $cleanNoRegDok = $sanitizeFileName($dokLegal->NoRegDok);
        $cleanJenisDok = $sanitizeFileName($dokLegal->JenisDok);
        $cleanPeruntukanDok = $sanitizeFileName($dokLegal->PeruntukanDok);

        // Buat nama file dengan format: NoRegDok_JenisDok_PeruntukanDok_TanggalTerbit
        $newFileName = $cleanNoRegDok . '_' .
            $cleanJenisDok . '_' .
            $cleanPeruntukanDok . '_' .
            $tanggalTerbit . '.' . $originalExtension;

        // Menyiapkan file untuk diunduh dengan nama yang telah diformat
        return Storage::disk('public')->download(
            'uploads/dokumen/' . $dokLegal->FileDok,
            $newFileName
        );
    }

    /**
     * Get dokumen by status for dashboard.
     */
    public function getDokumenByStatus()
    {
        // Dokumen aktif (tanggal berakhir > hari ini)
        $aktif = DokLegal::whereNotNull('TglBerakhirDok')
            ->where('TglBerakhirDok', '>', now())
            ->where('TglBerakhirDok', '>', now()->addDays(30))
            ->count();

        // Dokumen hampir kedaluwarsa (tanggal berakhir dalam 30 hari)
        $hampirKedaluwarsa = DokLegal::whereNotNull('TglBerakhirDok')
            ->where('TglBerakhirDok', '>', now())
            ->where('TglBerakhirDok', '<=', now()->addDays(15))
            ->count();

        // Dokumen kedaluwarsa (tanggal berakhir <= hari ini)
        $kedaluwarsa = DokLegal::whereNotNull('TglBerakhirDok')
            ->where('TglBerakhirDok', '<=', now())
            ->count();

        // Dokumen tetap (tidak ada tanggal berakhir)
        $tetap = DokLegal::whereNull('TglBerakhirDok')
            ->orWhere('JnsMasaBerlaku', 'Tetap')
            ->count();

        return response()->json([
            'aktif' => $aktif,
            'hampir_kedaluwarsa' => $hampirKedaluwarsa,
            'kedaluwarsa' => $kedaluwarsa,
            'tetap' => $tetap,
            'total' => DokLegal::count()
        ]);
    }

    /**
     * Get dokumen terbaru untuk dashboard.
     */
    public function getDokumenTerbaru()
    {
        $dokumenTerbaru = DokLegal::with(['perusahaan', 'kategori', 'jenis'])
            ->latest('TglTerbitDok')
            ->take(5)
            ->get(['id', 'IdKode', 'NoRegDok', 'DokPerusahaan', 'perusahaan_id', 'KategoriDok', 'kategori_id', 'JenisDok', 'jenis_id', 'TglTerbitDok', 'TglBerakhirDok']);

        return response()->json($dokumenTerbaru);
    }

    /**
     * Get tanggal pengingat dokumen
     */
    public function getPengingat(DokLegal $dokLegal)
    {
        return response()->json([
            'id' => $dokLegal->id,
            'TglPengingat' => $dokLegal->TglPengingat ? $dokLegal->TglPengingat->format('Y-m-d') : null
        ]);
    }
    public function view(DokLegal $dokLegal)
    {
        // Pastikan file ada
        if (!$dokLegal->FileDok) {
            abort(404, 'File tidak ditemukan');
        }

        // Path file - PERLU DIREVISI: menggunakan path yang benar sesuai dengan store method
        $filePath = storage_path('app/public/uploads/dokumen/' . $dokLegal->FileDok);

        // Cek apakah file ada
        if (!file_exists($filePath)) {
            abort(404, 'File tidak ditemukan');
        }

        // Dapatkan informasi file
        $fileInfo = pathinfo($filePath);
        $extension = strtolower($fileInfo['extension']);

        // Tentukan content type berdasarkan ekstensi file
        $contentTypes = [
            'pdf' => 'application/pdf',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xls' => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'ppt' => 'application/vnd.ms-powerpoint',
            'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'txt' => 'text/plain',
        ];

        $contentType = $contentTypes[$extension] ?? 'application/octet-stream';

        // Untuk file yang dapat ditampilkan di browser (PDF, gambar, txt)
        if (in_array($extension, ['pdf', 'jpg', 'jpeg', 'png', 'gif', 'txt'])) {
            return response()->file($filePath, [
                'Content-Type' => $contentType,
                'Content-Disposition' => 'inline; filename="' . $dokLegal->FileDok . '"'
            ]);
        }

        // Untuk file Office (Word, Excel, PowerPoint) - gunakan Google Docs Viewer
        if (in_array($extension, ['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'])) {
            // PERLU DIREVISI: URL harus menunjuk ke direktori yang benar
            $fileUrl = url('storage/uploads/dokumen/' . $dokLegal->FileDok);
            $googleViewerUrl = 'https://docs.google.com/viewer?url=' . urlencode($fileUrl) . '&embedded=true';

            return view('dokLegal.viewer', [
                'dokLegal' => $dokLegal,
                'viewerUrl' => $googleViewerUrl,
                'fileName' => $dokLegal->FileDok
            ]);
        }

        // Jika file tidak dapat dipreview, redirect ke download
        return redirect()->route('dokLegal.download', $dokLegal);
    }
    // Tambahkan method baru ke DokLegalController.php
    public function getDocumentStats()
    {
        // Hitung dokumen yang expired berdasarkan TglPengingat atau TglBerakhirDok
        $expiredCount = DokLegal::where(function ($query) {
            // Dokumen dengan Tanggal Pengingat yang sudah lewat atau hari ini
            $query->whereNotNull('TglPengingat')
                ->where('TglPengingat', '<=', now());
        })->orWhere(function ($query) {
            // Atau dokumen dengan Tanggal Berakhir yang sudah lewat
            $query->whereNotNull('TglBerakhirDok')
                ->where('TglBerakhirDok', '<', now())
                // Dan tidak memiliki TglPengingat yang sudah tercakup di kondisi sebelumnya
                ->where(function ($q) {
                    $q->whereNull('TglPengingat')
                        ->orWhere('TglPengingat', '>', now());
                });
        })->count();

        // Hitung dokumen yang akan expired (warning)
        $warningCount = DokLegal::where(function ($query) {
            // Dokumen dengan Tanggal Pengingat dalam 30 hari ke depan
            $query->whereNotNull('TglPengingat')
                ->where('TglPengingat', '>', now())
                ->where('TglPengingat', '<=', now()->addDays(30));
        })->orWhere(function ($query) {
            // Atau dokumen dengan Tanggal Berakhir dalam 30 hari ke depan
            $query->whereNotNull('TglBerakhirDok')
                ->where('TglBerakhirDok', '>=', now())
                ->where('TglBerakhirDok', '<=', now()->addDays(30))
                // Dan tidak memiliki TglPengingat yang sudah tercakup di kondisi sebelumnya
                ->where(function ($q) {
                    $q->whereNull('TglPengingat')
                        ->orWhere('TglPengingat', '>', now()->addDays(30));
                });
        })->count();

        return response()->json([
            'expired' => $expiredCount,
            'warning' => $warningCount
        ]);
    }
}
