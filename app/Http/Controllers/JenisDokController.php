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
            $hasViewPermission = auth()->user()->hasAccess('jenis-dok', 'detail');
            $hasEditPermission = auth()->user()->hasAccess('jenis-dok', 'ubah');
            $hasDeletePermission = auth()->user()->hasAccess('jenis-dok', 'hapus');
            $hasCreatePermission = auth()->user()->hasAccess('jenis-dok', 'tambah');
        }

        return view('jenis-dok.index', compact(
            'jenisDoks',
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
        if (!auth()->user()->isAdmin() && !auth()->user()->hasAccess('jenis-dok', 'tambah')) {
            return redirect()->route('jenis-dok.index')
                ->with('error', 'Anda tidak memiliki hak akses untuk menambah jenis dokumen.');
        }

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
        // Check permission
        if (!auth()->user()->isAdmin() && !auth()->user()->hasAccess('jenis-dok', 'tambah')) {
            return redirect()->route('jenis-dok.index')
                ->with('error', 'Anda tidak memiliki hak akses untuk menambah jenis dokumen.');
        }

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

        // Prepare data with created_by and updated_by
        $data = $request->all();
        $data['created_by'] = auth()->user()->id;

        JenisDok::create($data);
        Alert::success('Berhasil', 'Data Jenis Dokumen Berhasil Ditambahkan.');
        return redirect()->route('jenis-dok.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(JenisDok $jenisDok)
    {
        // Check permission
        if (!auth()->user()->isAdmin() && !auth()->user()->hasAccess('jenis-dok', 'detail')) {
            return redirect()->route('jenis-dok.index')
                ->with('error', 'Anda tidak memiliki hak akses untuk melihat detail jenis dokumen.');
        }

        $jenisDok->load('kategori', 'dokumenLegal');
        return view('jenis-dok.show', compact('jenisDok'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(JenisDok $jenisDok)
    {
        // Check permission
        if (!auth()->user()->isAdmin() && !auth()->user()->hasAccess('jenis-dok', 'ubah')) {
            return redirect()->route('jenis-dok.index')
                ->with('error', 'Anda tidak memiliki hak akses untuk mengedit jenis dokumen.');
        }

        $kategoriDokumen = KategoriDok::orderBy('KategoriDok', 'asc')->get();
        return view('jenis-dok.edit', compact('jenisDok', 'kategoriDokumen'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, JenisDok $jenisDok)
    {
        // Check permission
        if (!auth()->user()->isAdmin() && !auth()->user()->hasAccess('jenis-dok', 'ubah')) {
            return redirect()->route('jenis-dok.index')
                ->with('error', 'Anda tidak memiliki hak akses untuk mengedit jenis dokumen.');
        }

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

        // Prepare data with updated_by
        $data = $request->all();
        $data['updated_by'] = auth()->user()->id;

        $jenisDok->update($data);
        Alert::success('Berhasil', 'Data Jenis Dokumen Berhasil Diperbarui.');
        return redirect()->route('jenis-dok.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(JenisDok $jenisDok)
    {
        // Check permission
        if (!auth()->user()->isAdmin() && !auth()->user()->hasAccess('jenis-dok', 'hapus')) {
            return redirect()->route('jenis-dok.index')
                ->with('error', 'Anda tidak memiliki hak akses untuk menghapus jenis dokumen.');
        }

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