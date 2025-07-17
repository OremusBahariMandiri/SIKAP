<?php

namespace App\Http\Controllers;

use App\Models\Perusahaan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use RealRashid\SweetAlert\Facades\Alert;

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
            'WebPrsh' => 'nullable|url|max:100',
            'TglBerdiri' => 'nullable|date',
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
            'TelpPrsh2' => 'max:20',
            'EmailPrsh2' => 'max:100',
            'WebPrsh' => 'nullable|url|max:100',
            'TglBerdiri' => 'nullable|date',
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
}
