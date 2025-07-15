<?php

namespace App\Http\Controllers;

use App\Models\JenisDok;
use App\Models\KategoriDok;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use RealRashid\SweetAlert\Facades\Alert;

class JenisDokController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $jenisDoks = JenisDok::with('kategori')->get();
        return view('jenis-dok.index', compact('jenisDoks'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Generate kode ID otomatis
        $idKode = JenisDok::generateIdKode();
        $kategoriDokumen = KategoriDok::orderBy('KategoriDok', 'asc')->get();

        return view('jenis-dok.create', compact('idKode', 'kategoriDokumen'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'IdKode' => 'required|string|max:10|unique:A05DmJenisDok,IdKode',
            'JenisDok' => 'required|string|max:100',
            'idKategoriDok' => 'required|exists:A04DmKategoriDok,id',
        ], [
            'idKategoriDok.required' => 'Kategori dokumen harus dipilih.',
            'idKategoriDok.exists' => 'Kategori dokumen yang dipilih tidak valid.',
        ]);

        if ($validator->fails()) {
            return redirect()->route('jenis-dok.create')
                ->withErrors($validator)
                ->withInput();
        }

        JenisDok::create($request->all());
        Alert::success('Berhasil', 'Data Jenis Dokumen Berhasil Ditambahkan.');
        return redirect()->route('jenis-dok.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(JenisDok $jenisDok)
    {
        $jenisDok->load('kategori', 'dokumenLegal');
        return view('jenis-dok.show', compact('jenisDok'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(JenisDok $jenisDok)
    {
        $kategoriDokumen = KategoriDok::orderBy('KategoriDok', 'asc')->get();
        return view('jenis-dok.edit', compact('jenisDok', 'kategoriDokumen'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, JenisDok $jenisDok)
    {
        $validator = Validator::make($request->all(), [
            'IdKode' => [
                'required',
                'string',
                'max:10',
                Rule::unique('A05DmJenisDok', 'IdKode')->ignore($jenisDok->id)
            ],
            'JenisDok' => 'required|string|max:100',
            'idKategoriDok' => 'required|exists:A04DmKategoriDok,id',
        ], [
            'idKategoriDok.required' => 'Kategori dokumen harus dipilih.',
            'idKategoriDok.exists' => 'Kategori dokumen yang dipilih tidak valid.',
        ]);

        if ($validator->fails()) {
            return redirect()->route('jenis-dok.edit', $jenisDok->id)
                ->withErrors($validator)
                ->withInput();
        }

        $jenisDok->update($request->all());
        Alert::success('Berhasil', 'Data Jenis Dokumen Berhasil Diperbarui.');
        return redirect()->route('jenis-dok.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(JenisDok $jenisDok)
    {
        // Check if jenis is being used by DokLegal
        if ($jenisDok->dokumenLegal()->count() > 0) {
            return redirect()->route('jenis-dok.index')
                ->with('error', 'Jenis Dokumen tidak dapat dihapus karena masih digunakan oleh dokumen legal.');
        }

        $jenisDok->delete();

        Alert::success('Berhasil', 'Data Jenis Dokumen Berhasil Dihapus.');
        return redirect()->route('jenis-dok.index');
    }
}