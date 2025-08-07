<?php

namespace App\Http\Controllers;

use App\Models\KategoriDok;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use RealRashid\SweetAlert\Facades\Alert;

class KategoriDokController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $kategoriDoks = KategoriDok::all();

        // Check if user is admin
        $isAdmin = auth()->user()->isAdmin();

        // If user is admin, grant all permissions
        if ($isAdmin) {
            $hasViewPermission = true;
            $hasEditPermission = true;
            $hasDeletePermission = true;
            $hasCreatePermission = true;
        } else {
            // Individual permission checks for non-admin users
            $hasViewPermission = auth()->user()->hasAccess('kategori-dok', 'detail');
            $hasEditPermission = auth()->user()->hasAccess('kategori-dok', 'ubah');
            $hasDeletePermission = auth()->user()->hasAccess('kategori-dok', 'hapus');
            $hasCreatePermission = auth()->user()->hasAccess('kategori-dok', 'tambah');
        }

        return view('kategori-dok.index', compact(
            'kategoriDoks',
            'hasViewPermission',
            'hasEditPermission',
            'hasDeletePermission',
            'hasCreatePermission',
            'isAdmin'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Check permission
        if (!auth()->user()->isAdmin() && !auth()->user()->hasAccess('kategori-dok', 'tambah')) {
            return redirect()->route('kategori-dok.index')
                ->with('error', 'Anda tidak memiliki hak akses untuk menambah kategori dokumen.');
        }

        // Generate kode ID otomatis
        $idKode = KategoriDok::generateIdKode();

        return view('kategori-dok.create', compact('idKode'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Check permission
        if (!auth()->user()->isAdmin() && !auth()->user()->hasAccess('kategori-dok', 'tambah')) {
            return redirect()->route('kategori-dok.index')
                ->with('error', 'Anda tidak memiliki hak akses untuk menambah kategori dokumen.');
        }

        $validator = Validator::make($request->all(), [
            'IdKode' => 'required|string|max:10|unique:A04DmKategoriDok,IdKode',
            'KategoriDok' => 'required|string|max:100',
        ]);

        if ($validator->fails()) {
            return redirect()->route('kategori-dok.create')
                ->withErrors($validator)
                ->withInput();
        }

        // Prepare data with created_by and updated_by
        $data = $request->all();
        $data['created_by'] = auth()->user()->id;

        KategoriDok::create($data);

        Alert::success('Berhasil', 'Data Kategori Dokumen Berhasil Ditambahkan.');
        return redirect()->route('kategori-dok.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(KategoriDok $kategoriDok)
    {
        // Check permission
        if (!auth()->user()->isAdmin() && !auth()->user()->hasAccess('kategori-dok', 'detail')) {
            return redirect()->route('kategori-dok.index')
                ->with('error', 'Anda tidak memiliki hak akses untuk melihat detail kategori dokumen.');
        }

        return view('kategori-dok.show', compact('kategoriDok'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(KategoriDok $kategoriDok)
    {
        // Check permission
        if (!auth()->user()->isAdmin() && !auth()->user()->hasAccess('kategori-dok', 'ubah')) {
            return redirect()->route('kategori-dok.index')
                ->with('error', 'Anda tidak memiliki hak akses untuk mengedit kategori dokumen.');
        }

        return view('kategori-dok.edit', compact('kategoriDok'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, KategoriDok $kategoriDok)
    {
        // Check permission
        if (!auth()->user()->isAdmin() && !auth()->user()->hasAccess('kategori-dok', 'ubah')) {
            return redirect()->route('kategori-dok.index')
                ->with('error', 'Anda tidak memiliki hak akses untuk mengedit kategori dokumen.');
        }

        $validator = Validator::make($request->all(), [
            'IdKode' => [
                'required',
                'string',
                'max:10',
                Rule::unique('A04DmKategoriDok', 'IdKode')->ignore($kategoriDok->id)
            ],
            'KategoriDok' => 'required|string|max:100',
        ]);

        if ($validator->fails()) {
            return redirect()->route('kategori-dok.edit', $kategoriDok->id)
                ->withErrors($validator)
                ->withInput();
        }

        // Prepare data with updated_by
        $data = $request->all();
        $data['updated_by'] = auth()->user()->id;

        $kategoriDok->update($data);

        Alert::success('Berhasil', 'Data Kategori Dokumen Berhasil Diperbarui.');
        return redirect()->route('kategori-dok.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(KategoriDok $kategoriDok)
    {
        // Check permission
        if (!auth()->user()->isAdmin() && !auth()->user()->hasAccess('kategori-dok', 'hapus')) {
            return redirect()->route('kategori-dok.index')
                ->with('error', 'Anda tidak memiliki hak akses untuk menghapus kategori dokumen.');
        }

        // Check if kategori is being used by DokLegal
        if ($kategoriDok->dokumenLegal()->count() > 0) {
            return redirect()->route('kategori-dok.index')
                ->with('error', 'Kategori Dokumen tidak dapat dihapus karena masih digunakan oleh dokumen legal.');
        }

        $kategoriDok->delete();

        Alert::success('Berhasil', 'Data Kategori Dokumen Berhasil Dihapus.');
        return redirect()->route('kategori-dok.index');
    }
}