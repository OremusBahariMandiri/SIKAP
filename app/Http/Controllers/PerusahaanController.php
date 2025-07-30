<?php

namespace App\Http\Controllers;

use App\Models\Perusahaan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use RealRashid\SweetAlert\Facades\Alert;
use App\Exports\PerusahaanExport;
use Maatwebsite\Excel\Facades\Excel;

class PerusahaanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $perusahaans = Perusahaan::all();
        return view('perusahaan.index', compact('perusahaans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Generate kode ID otomatis
        $idKode = Perusahaan::generateIdKode();

        return view('perusahaan.create', compact('idKode'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'IdKode' => 'required|string|max:10|unique:A03DmPerusahaan,IdKode',
            'NamaPrsh' => 'required|string|max:100',
            'AlamatPrsh' => 'required|string',
            'TelpPrsh' => 'required|string|max:20',
            'EmailPrsh' => 'required|email|max:100',
            'TelpPrsh2' => 'nullable|string|max:20',
            'EmailPrsh2' => 'nullable|email|max:100',
            'WebPrsh' => 'nullable|url|max:100',
            'TglBerdiri' => 'nullable|date',
            'BidangUsh' => 'nullable|string|max:100',
            'IzinUsh' => 'nullable|string|max:100',
            'GolonganUsh' => 'nullable|string|max:50',
            'DirekturUtm' => 'nullable|string|max:100',
            'Direktur' => 'nullable|string|max:100',
            'KomisarisUtm' => 'nullable|string|max:100',
            'Komisaris' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return redirect()->route('perusahaan.create')
                ->withErrors($validator)
                ->withInput();
        }

        // Prepare data with created_by and updated_by
        $data = $request->all();
        $data['created_by'] = auth()->user()->id;

        Perusahaan::create($data);

        Alert::success('Berhasil', 'Data Perusahaan Berhasil Ditambahkan.');
        return redirect()->route('perusahaan.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Perusahaan $perusahaan)
    {
        return view('perusahaan.show', compact('perusahaan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Perusahaan $perusahaan)
    {
        return view('perusahaan.edit', compact('perusahaan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Perusahaan $perusahaan)
    {
        $validator = Validator::make($request->all(), [
            'IdKode' => [
                'required',
                'string',
                'max:10',
                Rule::unique('A03DmPerusahaan', 'IdKode')->ignore($perusahaan->id)
            ],
            'NamaPrsh' => 'required|string|max:100',
            'AlamatPrsh' => 'required|string',
            'TelpPrsh' => 'required|string|max:20',
            'EmailPrsh' => 'required|email|max:100',
            'TelpPrsh2' => 'nullable|string|max:20',
            'EmailPrsh2' => 'nullable|email|max:100',
            'WebPrsh' => 'nullable|url|max:100',
            'TglBerdiri' => 'nullable|date',
            'BidangUsh' => 'nullable|string|max:100',
            'IzinUsh' => 'nullable|string|max:100',
            'GolonganUsh' => 'nullable|string|max:50',
            'DirekturUtm' => 'nullable|string|max:100',
            'Direktur' => 'nullable|string|max:100',
            'KomisarisUtm' => 'nullable|string|max:100',
            'Komisaris' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return redirect()->route('perusahaan.edit', $perusahaan->id)
                ->withErrors($validator)
                ->withInput();
        }

        // Prepare data with updated_by
        $data = $request->all();
        $data['updated_by'] = auth()->user()->id;

        $perusahaan->update($data);
        Alert::success('Berhasil', 'Data Perusahaan Berhasil Diperbarui.');
        return redirect()->route('perusahaan.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Perusahaan $perusahaan)
    {
        // Check if perusahaan is being used by DokLegal
        if ($perusahaan->dokumenLegal()->count() > 0) {
            return redirect()->route('perusahaan.index')
                ->with('error', 'Perusahaan tidak dapat dihapus karena masih digunakan oleh dokumen legal.');
        }

        $perusahaan->delete();

        Alert::success('Berhasil', 'Data Perusahaan Berhasil Dihapus.');
        return redirect()->route('perusahaan.index');
    }

    public function exportExcel(Request $request)
    {
        // Filter berdasarkan parameter yang diterima dari form
        $query = Perusahaan::query();

        // Membuat array untuk menyimpan filter yang diterapkan
        $appliedFilters = [];

        // Filter Nama Perusahaan
        if ($request->filled('filter_nama')) {
            $query->where('NamaPrsh', $request->filter_nama);
            $appliedFilters['nama'] = $request->filter_nama;
        }

        // Filter Bidang Usaha
        if ($request->filled('filter_bidang')) {
            $query->where('BidangUsh', $request->filter_bidang);
            $appliedFilters['bidang'] = $request->filter_bidang;
        }

        // Filter Izin Usaha
        if ($request->filled('filter_izin')) {
            $query->where('IzinUsh', $request->filter_izin);
            $appliedFilters['izin'] = $request->filter_izin;
        }

        // Filter Golongan Usaha
        if ($request->filled('filter_golongan')) {
            $query->where('GolonganUsh', $request->filter_golongan);
            $appliedFilters['golongan'] = $request->filter_golongan;
        }

        // Filter Direktur Utama
        if ($request->filled('filter_direktur_utama')) {
            $query->where('DirekturUtm', $request->filter_direktur_utama);
            $appliedFilters['direktur_utama'] = $request->filter_direktur_utama;
        }

        // Filter Direktur
        if ($request->filled('filter_direktur')) {
            $query->where('Direktur', $request->filter_direktur);
            $appliedFilters['direktur'] = $request->filter_direktur;
        }

        // Filter Komisaris Utama
        if ($request->filled('filter_komisaris_utama')) {
            $query->where('KomisarisUtm', $request->filter_komisaris_utama);
            $appliedFilters['komisaris_utama'] = $request->filter_komisaris_utama;
        }

        // Filter Komisaris
        if ($request->filled('filter_komisaris')) {
            $query->where('Komisaris', $request->filter_komisaris);
            $appliedFilters['komisaris'] = $request->filter_komisaris;
        }

        // Filter Telepon
        if ($request->filled('filter_telepon')) {
            $query->where('TelpPrsh', $request->filter_telepon);
            $appliedFilters['telepon'] = $request->filter_telepon;
        }

        // Filter Email
        if ($request->filled('filter_email')) {
            $query->where('EmailPrsh', $request->filter_email);
            $appliedFilters['email'] = $request->filter_email;
        }

        // Filter Website
        if ($request->filled('filter_website')) {
            $query->where('WebPrsh', $request->filter_website);
            $appliedFilters['website'] = $request->filter_website;
        }

        // Filter Tanggal Berdiri
        if ($request->filled('filter_tgl_berdiri_from')) {
            $query->whereDate('TglBerdiri', '>=', $request->filter_tgl_berdiri_from);
            $appliedFilters['tgl_berdiri_from'] = $request->filter_tgl_berdiri_from;
        }

        if ($request->filled('filter_tgl_berdiri_to')) {
            $query->whereDate('TglBerdiri', '<=', $request->filter_tgl_berdiri_to);
            $appliedFilters['tgl_berdiri_to'] = $request->filter_tgl_berdiri_to;
        }

        // Ambil data berdasarkan filter
        $perusahaans = $query->get();

        // Buat file Excel dengan data yang sudah difilter
        return Excel::download(new PerusahaanExport($perusahaans, $appliedFilters), 'data_perusahaan.xlsx');
    }
}
