<?php

namespace App\Http\Controllers;

use App\Exports\DokLegalExport;
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
use Illuminate\Support\Facades\Log;
use DateInterval;
use DatePeriod;
use DateTime;
use Maatwebsite\Excel\Facades\Excel;
use RealRashid\SweetAlert\Facades\Alert;

class DokLegalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Kode yang sudah ada
        // ...
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Kode yang sudah ada
        // ...
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Log semua info request yang diterima
        Log::info('Memulai proses penambahan dokumen legal baru');
        Log::info('Request data:', $request->except(['file_dokumen']));

        try {
            // Log informasi detail tentang file yang diupload
            if ($request->hasFile('file_dokumen')) {
                $file = $request->file('file_dokumen');

                // Log informasi dasar file
                Log::info('File info detail:', [
                    'originalName' => $file->getClientOriginalName(),
                    'mimeType' => $file->getMimeType(),
                    'size' => $file->getSize(),
                    'extension' => $file->getClientOriginalExtension(),
                    'error' => $file->getError(),
                    'isValid' => $file->isValid(),
                    'hashName' => $file->hashName(),
                    'realPath' => $file->getRealPath(),
                    'tempFilePath' => $file->getPathname()
                ]);

                // Untuk file PDF, cek informasi lebih detail
                if ($file->getClientOriginalExtension() === 'pdf' || $file->getMimeType() === 'application/pdf') {
                    Log::info('Mendeteksi file PDF, melakukan pemeriksaan tambahan');

                    // Cek apakah file dapat dibaca
                    if (file_exists($file->getRealPath())) {
                        $fileSize = filesize($file->getRealPath());
                        $isReadable = is_readable($file->getRealPath());

                        Log::info('Pemeriksaan file PDF:', [
                            'exists' => true,
                            'fileSize' => $fileSize,
                            'isReadable' => $isReadable
                        ]);

                        // Coba baca beberapa byte awal untuk memastikan format PDF
                        try {
                            $handle = fopen($file->getRealPath(), 'r');
                            $header = fread($handle, 5); // Baca 5 byte pertama
                            fclose($handle);

                            Log::info('Header file PDF:', [
                                'header' => bin2hex($header),
                                'isPDFFormat' => (substr($header, 0, 4) === '%PDF')
                            ]);
                        } catch (\Exception $e) {
                            Log::error('Gagal membaca header file PDF: ' . $e->getMessage());
                        }
                    } else {
                        Log::warning('File PDF tidak ada di path sementara', [
                            'path' => $file->getRealPath()
                        ]);
                    }
                }
            } else {
                Log::warning('File dokumen tidak ditemukan dalam request. Memeriksa request secara mendetail.');

                // Log input file dari request untuk debugging
                if ($request->hasFile('file_dokumen')) {
                    Log::info('hasFile() mengembalikan true tapi validasi lain gagal');
                } else {
                    Log::warning('hasFile() mengembalikan false');
                }

                // Periksa semua file yang dikirim
                if ($request->allFiles()) {
                    Log::info('Semua file dalam request:', array_keys($request->allFiles()));
                } else {
                    Log::warning('Tidak ada file yang ditemukan dalam request');
                }

                // Periksa request headers
                Log::info('Request headers:', [
                    'Content-Type' => $request->header('Content-Type'),
                    'Content-Length' => $request->header('Content-Length')
                ]);
            }

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
                'file_dokumen' => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx|max:20480', // Maksimal 20MB
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
                // Pesan-pesan validasi yang sudah ada
                'file_dokumen.required' => 'File Dokumen wajib diunggah',
                'file_dokumen.file' => 'Upload harus berupa file yang valid',
                'file_dokumen.mimes' => 'Format file tidak didukung. Gunakan PDF, JPG, PNG, DOC, DOCX, XLS, atau XLSX',
                'file_dokumen.max' => 'Ukuran file tidak boleh lebih dari 20MB',
            ];

            Log::info('Validasi form dimulai');

            // Validasi request
            $validated = $request->validate($rules, $messages);

            Log::info('Validasi form berhasil');

            // Kode untuk mengolah data setelah validasi
            // ...

            // Handle file upload
            if ($request->hasFile('file_dokumen')) {
                try {
                    Log::info('Memulai proses upload file');

                    $file = $request->file('file_dokumen');
                    $fileName = time() . '_' . $file->getClientOriginalName();

                    // Periksa ekstensi file dan perlakukan PDF secara khusus
                    $extension = strtolower($file->getClientOriginalExtension());

                    if ($extension === 'pdf') {
                        Log::info('Menangani file PDF dengan perlakuan khusus');

                        // Buat direktori jika belum ada
                        $uploadDir = 'uploads/dokumen';
                        if (!Storage::disk('public')->exists($uploadDir)) {
                            Storage::disk('public')->makeDirectory($uploadDir);
                            Log::info('Membuat direktori uploads/dokumen');
                        }

                        // Simpan dengan metode alternatif untuk file PDF
                        $filePath = $file->getRealPath();
                        $fileContents = file_get_contents($filePath);

                        if ($fileContents === false) {
                            Log::error('Gagal membaca isi file PDF', [
                                'filePath' => $filePath
                            ]);
                            throw new \Exception('Gagal membaca isi file PDF');
                        }

                        $savePath = 'uploads/dokumen/' . $fileName;
                        $saveResult = Storage::disk('public')->put($savePath, $fileContents);

                        if ($saveResult) {
                            Log::info('File PDF berhasil disimpan dengan metode alternatif', [
                                'path' => $savePath
                            ]);
                            $validated['FileDok'] = $fileName;
                        } else {
                            Log::error('Gagal menyimpan file PDF dengan metode alternatif');
                            throw new \Exception('Gagal menyimpan file PDF ke storage');
                        }
                    } else {
                        // Metode normal untuk file non-PDF
                        Log::info('Menyimpan file non-PDF ke storage', [
                            'path' => 'uploads/dokumen',
                            'fileName' => $fileName
                        ]);

                        $uploadPath = $file->storeAs('uploads/dokumen', $fileName, 'public');

                        if ($uploadPath) {
                            Log::info('File berhasil disimpan', ['path' => $uploadPath]);
                            $validated['FileDok'] = $fileName;
                        } else {
                            Log::error('Gagal menyimpan file ke storage');
                            throw new \Exception('Gagal menyimpan file ke storage');
                        }
                    }
                } catch (\Exception $e) {
                    Log::error('Error saat upload file: ' . $e->getMessage(), [
                        'exception' => $e,
                        'trace' => $e->getTraceAsString()
                    ]);

                    return back()->withInput()->withErrors(['file_dokumen' => 'Terjadi kesalahan saat mengunggah file: ' . $e->getMessage()]);
                }
            } else {
                Log::warning('Tidak ada file yang diunggah setelah validasi');
                return back()->withInput()->withErrors(['file_dokumen' => 'File dokumen wajib diunggah']);
            }

            // Ambil data master berdasarkan ID
            Log::info('Mengambil data master');

            $perusahaan = Perusahaan::findOrFail($request->perusahaan_id);
            $kategori = KategoriDok::findOrFail($request->kategori_id);
            $jenis = JenisDok::findOrFail($request->jenis_id);

            // Set nilai dari master data
            $validated['DokPerusahaan'] = $perusahaan->NamaPrsh;
            $validated['KategoriDok'] = $kategori->KategoriDok;
            $validated['JenisDok'] = $jenis->JenisDok;

            // Tambahkan informasi user yang membuat data
            $validated['created_by'] = auth()->user()->id;

            Log::info('Menyimpan data dokumen ke database');
            $dokLegal = DokLegal::create($validated);
            Log::info('Data dokumen berhasil disimpan', ['id' => $dokLegal->id]);

            Alert::success('Berhasil', 'Data Dokumen Legal Berhasil Ditambahkan.');
            return redirect()->route('dokLegal.index');
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validasi error: ' . json_encode($e->errors()));
            throw $e;
        } catch (\Exception $e) {
            Log::error('Error saat menambahkan dokumen: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);

            Alert::error('Gagal', 'Terjadi kesalahan saat menyimpan dokumen: ' . $e->getMessage());
            return back()->withInput()->withErrors(['general' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DokLegal $dokLegal)
    {
        Log::info('Memulai proses update dokumen legal', ['id' => $dokLegal->id]);
        Log::info('Request data:', $request->except(['file_dokumen']));

        try {
            // Log informasi detail tentang file yang diupload (jika ada)
            if ($request->hasFile('file_dokumen')) {
                $file = $request->file('file_dokumen');

                // Log informasi dasar file
                Log::info('File info detail untuk update:', [
                    'originalName' => $file->getClientOriginalName(),
                    'mimeType' => $file->getMimeType(),
                    'size' => $file->getSize(),
                    'extension' => $file->getClientOriginalExtension(),
                    'error' => $file->getError(),
                    'isValid' => $file->isValid(),
                    'hashName' => $file->hashName(),
                    'realPath' => $file->getRealPath(),
                    'tempFilePath' => $file->getPathname()
                ]);

                // Untuk file PDF, cek informasi lebih detail
                if ($file->getClientOriginalExtension() === 'pdf' || $file->getMimeType() === 'application/pdf') {
                    Log::info('Mendeteksi file PDF untuk update, melakukan pemeriksaan tambahan');

                    // Cek apakah file dapat dibaca
                    if (file_exists($file->getRealPath())) {
                        $fileSize = filesize($file->getRealPath());
                        $isReadable = is_readable($file->getRealPath());

                        Log::info('Pemeriksaan file PDF untuk update:', [
                            'exists' => true,
                            'fileSize' => $fileSize,
                            'isReadable' => $isReadable
                        ]);
                    } else {
                        Log::warning('File PDF untuk update tidak ada di path sementara', [
                            'path' => $file->getRealPath()
                        ]);
                    }
                }
            }

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
                    'max:50',
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
                'file_dokumen' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx|max:20480',
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
                // Pesan-pesan validasi yang sudah ada
                'file_dokumen.file' => 'Upload harus berupa file yang valid',
                'file_dokumen.mimes' => 'Format file tidak didukung. Gunakan PDF, JPG, PNG, DOC, DOCX, XLS, atau XLSX',
                'file_dokumen.max' => 'Ukuran file tidak boleh lebih dari 20MB',
            ];

            Log::info('Validasi form update dimulai');

            // Validasi request
            $validated = $request->validate($rules, $messages);

            Log::info('Validasi form update berhasil');

            // Kode untuk mengolah data setelah validasi
            // ...

            // Handle file upload
            if ($request->hasFile('file_dokumen')) {
                try {
                    Log::info('Memulai proses update file');

                    // Check old file existence and delete
                    if ($dokLegal->FileDok) {
                        $oldFilePath = 'uploads/dokumen/' . $dokLegal->FileDok;
                        Log::info('Memeriksa file lama', ['path' => $oldFilePath]);

                        if (Storage::disk('public')->exists($oldFilePath)) {
                            Log::info('Menghapus file lama');
                            Storage::disk('public')->delete($oldFilePath);
                            Log::info('File lama berhasil dihapus');
                        } else {
                            Log::warning('File lama tidak ditemukan', ['path' => $oldFilePath]);
                        }
                    }

                    $file = $request->file('file_dokumen');
                    $fileName = time() . '_' . $file->getClientOriginalName();

                    // Periksa ekstensi file dan perlakukan PDF secara khusus
                    $extension = strtolower($file->getClientOriginalExtension());

                    if ($extension === 'pdf') {
                        Log::info('Menangani file PDF dengan perlakuan khusus untuk update');

                        // Buat direktori jika belum ada
                        $uploadDir = 'uploads/dokumen';
                        if (!Storage::disk('public')->exists($uploadDir)) {
                            Storage::disk('public')->makeDirectory($uploadDir);
                            Log::info('Membuat direktori uploads/dokumen');
                        }

                        // Simpan dengan metode alternatif untuk file PDF
                        $filePath = $file->getRealPath();
                        $fileContents = file_get_contents($filePath);

                        if ($fileContents === false) {
                            Log::error('Gagal membaca isi file PDF untuk update', [
                                'filePath' => $filePath
                            ]);
                            throw new \Exception('Gagal membaca isi file PDF');
                        }

                        $savePath = 'uploads/dokumen/' . $fileName;
                        $saveResult = Storage::disk('public')->put($savePath, $fileContents);

                        if ($saveResult) {
                            Log::info('File PDF update berhasil disimpan dengan metode alternatif', [
                                'path' => $savePath
                            ]);
                            $validated['FileDok'] = $fileName;
                        } else {
                            Log::error('Gagal menyimpan file PDF update dengan metode alternatif');
                            throw new \Exception('Gagal menyimpan file PDF ke storage');
                        }
                    } else {
                        // Metode normal untuk file non-PDF
                        Log::info('Menyimpan file non-PDF update ke storage', [
                            'path' => 'uploads/dokumen',
                            'fileName' => $fileName
                        ]);

                        $uploadPath = $file->storeAs('uploads/dokumen', $fileName, 'public');

                        if ($uploadPath) {
                            Log::info('File update berhasil disimpan', ['path' => $uploadPath]);
                            $validated['FileDok'] = $fileName;
                        } else {
                            Log::error('Gagal menyimpan file update ke storage');
                            throw new \Exception('Gagal menyimpan file ke storage');
                        }
                    }
                } catch (\Exception $e) {
                    Log::error('Error saat update file: ' . $e->getMessage(), [
                        'exception' => $e,
                        'trace' => $e->getTraceAsString()
                    ]);

                    return back()->withInput()->withErrors(['file_dokumen' => 'Terjadi kesalahan saat mengunggah file: ' . $e->getMessage()]);
                }
            } else {
                Log::info('Tidak ada file baru yang diunggah, menggunakan file yang ada');
            }

            // Ambil data master berdasarkan ID
            Log::info('Mengambil data master untuk update');

            $perusahaan = Perusahaan::findOrFail($request->perusahaan_id);
            $kategori = KategoriDok::findOrFail($request->kategori_id);
            $jenis = JenisDok::findOrFail($request->jenis_id);

            // Set nilai dari master data
            $validated['DokPerusahaan'] = $perusahaan->NamaPrsh;
            $validated['KategoriDok'] = $kategori->KategoriDok;
            $validated['JenisDok'] = $jenis->JenisDok;

            // Update informasi user yang mengupdate data
            $validated['updated_by'] = auth()->user()->id;

            Log::info('Memperbarui data dokumen di database', ['id' => $dokLegal->id]);
            $dokLegal->update($validated);
            Log::info('Data dokumen berhasil diperbarui');

            Alert::success('Berhasil', 'Data Dokumen Legal Berhasil Diperbarui.');
            return redirect()->route('dokLegal.index');
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validasi error update: ' . json_encode($e->errors()));
            throw $e;
        } catch (\Exception $e) {
            Log::error('Error saat memperbarui dokumen: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);

            Alert::error('Gagal', 'Terjadi kesalahan saat memperbarui dokumen: ' . $e->getMessage());
            return back()->withInput()->withErrors(['general' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DokLegal $dokLegal)
    {
        Log::info('Memulai proses penghapusan dokumen', ['id' => $dokLegal->id]);

        // Verifikasi hak akses terlebih dahulu
        if (!auth()->user()->hasAccess('dokLegal', 'HapusAcs')) {
            // Log percobaan akses tidak sah
            Log::warning('Upaya penghapusan dokumen tanpa izin', [
                'user_id' => auth()->user()->id,
                'dokumen_id' => $dokLegal->id
            ]);

            // Redirect dengan pesan error
            return redirect()->route('dokLegal.index')
                ->with('error', 'Anda tidak memiliki hak akses untuk menghapus dokumen.');
        }

        try {
            // Delete file if exists
            if ($dokLegal->FileDok) {
                $filePath = 'uploads/dokumen/' . $dokLegal->FileDok;
                Log::info('Memeriksa file untuk dihapus', ['path' => $filePath]);

                if (Storage::disk('public')->exists($filePath)) {
                    Log::info('Menghapus file dokumen');
                    Storage::disk('public')->delete($filePath);
                    Log::info('File dokumen berhasil dihapus');
                } else {
                    Log::warning('File dokumen tidak ditemukan saat penghapusan', ['path' => $filePath]);
                }
            }

            Log::info('Menghapus data dokumen dari database');
            $dokLegal->delete();
            Log::info('Data dokumen berhasil dihapus');

            Alert::success('Berhasil', 'Data Dokumen Legal Berhasil Dihapus.');
            return redirect()->route('dokLegal.index');
        } catch (\Exception $e) {
            Log::error('Error saat menghapus dokumen: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('dokLegal.index')
                ->with('error', 'Terjadi kesalahan saat menghapus dokumen: ' . $e->getMessage());
        }
    }


    public function download(DokLegal $dokLegal)
    {
        Log::info('Memulai proses download dokumen', ['id' => $dokLegal->id, 'file' => $dokLegal->FileDok]);

        if (!$dokLegal->FileDok) {
            Log::warning('Percobaan download dokumen tanpa file', ['id' => $dokLegal->id]);
            return back()->with('error', 'File tidak ditemukan.');
        }

        $filePath = 'uploads/dokumen/' . $dokLegal->FileDok;

        if (!Storage::disk('public')->exists($filePath)) {
            Log::warning('File dokumen tidak ditemukan saat download', [
                'id' => $dokLegal->id,
                'path' => $filePath
            ]);
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

        Log::info('Download dokumen', [
            'id' => $dokLegal->id,
            'original_file' => $dokLegal->FileDok,
            'download_name' => $newFileName
        ]);

        // Menyiapkan file untuk diunduh dengan nama yang telah diformat
        return Storage::disk('public')->download(
            $filePath,
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

    public function exportExcel(Request $request)
    {
        // Ambil parameter filter
        $filters = [
            'noreg' => $request->filter_noreg,
            'perusahaan' => $request->filter_perusahaan,
            'kategori' => $request->filter_kategori,
            'jenis' => $request->filter_jenis,
            'peruntukan' => $request->filter_peruntukan,
            'atas_nama' => $request->filter_atas_nama,
            'tgl_terbit_from' => $request->filter_tgl_terbit_from,
            'tgl_terbit_to' => $request->filter_tgl_terbit_to,
            'tgl_berakhir_from' => $request->filter_tgl_berakhir_from,
            'tgl_berakhir_to' => $request->filter_tgl_berakhir_to,
            'sts_berlaku' => $request->filter_sts_berlaku,
        ];

        // Query data berdasarkan filter
        $query = DokLegal::query();

        // Filter No Registrasi
        if ($request->filled('filter_noreg')) {
            $query->where('NoRegDok', 'like', '%' . $request->filter_noreg . '%');
        }

        // Filter Perusahaan
        if ($request->filled('filter_perusahaan')) {
            $query->where('DokPerusahaan', $request->filter_perusahaan);
        }

        // Filter Kategori
        if ($request->filled('filter_kategori')) {
            $query->where('KategoriDok', $request->filter_kategori);
        }

        // Filter Jenis Dokumen
        if ($request->filled('filter_jenis')) {
            $query->where('JenisDok', $request->filter_jenis);
        }

        // Filter Peruntukan
        if ($request->filled('filter_peruntukan')) {
            $query->where('PeruntukanDok', $request->filter_peruntukan);
        }

        // Filter Atas Nama
        if ($request->filled('filter_atas_nama')) {
            $query->where('DokAtasNama', $request->filter_atas_nama);
        }

        // Filter Tanggal Terbit
        if ($request->filled('filter_tgl_terbit_from')) {
            $query->where('TglTerbitDok', '>=', $request->filter_tgl_terbit_from);
        }
        if ($request->filled('filter_tgl_terbit_to')) {
            $query->where('TglTerbitDok', '<=', $request->filter_tgl_terbit_to);
        }

        // Filter Tanggal Berakhir
        if ($request->filled('filter_tgl_berakhir_from')) {
            $query->where('TglBerakhirDok', '>=', $request->filter_tgl_berakhir_from);
        }
        if ($request->filled('filter_tgl_berakhir_to')) {
            $query->where('TglBerakhirDok', '<=', $request->filter_tgl_berakhir_to);
        }

        // Filter Status Dokumen
        if ($request->filled('filter_sts_berlaku')) {
            $query->where('StsBerlakuDok', $request->filter_sts_berlaku);
        }

        $dokLegals = $query->get();

        // Format tanggal untuk nama file
        $currentDate = now()->format('d-m-Y_H-i-s');
        $fileName = 'Dokumen_Legal_' . $currentDate . '.xlsx';

        return Excel::download(new DokLegalExport($dokLegals, $filters), $fileName);
    }
}
