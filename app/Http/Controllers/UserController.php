<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::all();
        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Generate kode ID otomatis
        $idKode = User::generateIdKode();

        // Daftar wilayah kerja untuk dropdown
        $wilayahKerja = [
            'Samarinda' => 'Samarinda',
            'Balikpapan' => 'Balikpapan',
            'Surabaya' => 'Surabaya',
            'Lamongan' => 'Lamongan',
            'Gresik' => 'Gresik',
            'Samboja' => 'Samboja',
            'Bontang' => 'Bontang',
            'Makassar' => 'Makassar',

        ];

        // Daftar departemen untuk dropdown
        $departemen = [
            'Kesekretariatan' => 'Kesekretariatan',
            'HRD' => 'HRD',
            'Keuangan' => 'Keuangan',
            'Komersial' => 'Komersial',
            'Operations' => 'Operations',
            'HSE' => 'HSE'
        ];

        return view('users.create', compact('idKode', 'wilayahKerja', 'departemen'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'IdKode' => 'required|string|max:10|unique:A01DmUser,IdKode',
            'NikKry' => 'required|string|max:20|unique:A01DmUser,NikKry',
            'NamaKry' => 'required|string|max:100',
            'DepartemenKry' => 'required|string|max:50',
            'JabatanKry' => 'required|string|max:50',
            'WilkerKry' => 'required|string|max:50',
            'PasswordKry' => 'required|string|min:6',
            'password_confirmation' => 'required|same:PasswordKry',
        ]);

        if ($validator->fails()) {
            return redirect()->route('users.create')
                ->withErrors($validator)
                ->withInput();
        }

        $user = new User();
        $user->IdKode = $request->IdKode;
        $user->NikKry = $request->NikKry;
        $user->NamaKry = $request->NamaKry;
        $user->DepartemenKry = $request->DepartemenKry;
        $user->JabatanKry = $request->JabatanKry;
        $user->WilkerKry = $request->WilkerKry;
        $user->PasswordKry = Hash::make($request->PasswordKry);
        $user->is_admin = $request->has('is_admin');
        $user['created_by'] = auth()->user()->id; // Use the actual 'id' property
        $user->save();

        Alert::success('Berhasil', 'Data Pengguna Berhasil Ditambahkan.');
        return redirect()->route('users.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        // Daftar wilayah kerja untuk dropdown
        $wilayahKerja = [
            'Samarinda' => 'Samarinda',
            'Balikpapan' => 'Balikpapan',
            'Surabaya' => 'Surabaya',
            'Lamongan' => 'Lamongan',
            'Gresik' => 'Gresik',
            'Samboja' => 'Samboja',
            'Bontang' => 'Bontang',
            'Makassar' => 'Makassar',
        ];

        // Daftar departemen untuk dropdown
        $departemen = [
            'Kesekretariatan' => 'Kesekretariatan',
            'HRD' => 'HRD',
            'Keuangan' => 'Keuangan',
            'Komersial' => 'Komersial',
            'Operations' => 'Operations',
            'HSE' => 'HSE'
        ];

        return view('users.edit', compact('user', 'wilayahKerja', 'departemen'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'IdKode' => [
                'required',
                'string',
                'max:10',
                Rule::unique('A01DmUser', 'IdKode')->ignore($user->id)
            ],
            'NikKry' => [
                'required',
                'string',
                'max:20',
                Rule::unique('A01DmUser', 'NikKry')->ignore($user->id)
            ],
            'NamaKry' => 'required|string|max:100',
            'DepartemenKry' => 'required|string|max:50',
            'JabatanKry' => 'required|string|max:50',
            'WilkerKry' => 'required|string|max:50',
            'PasswordKry' => 'nullable|string|min:6',
            'password_confirmation' => 'nullable|same:PasswordKry',
        ]);

        if ($validator->fails()) {
            return redirect()->route('users.edit', $user->id)
                ->withErrors($validator)
                ->withInput();
        }

        $user->IdKode = $request->IdKode;
        $user->NikKry = $request->NikKry;
        $user->NamaKry = $request->NamaKry;
        $user->DepartemenKry = $request->DepartemenKry;
        $user->JabatanKry = $request->JabatanKry;
        $user->WilkerKry = $request->WilkerKry;
        $user->is_admin = $request->has('is_admin');

        if ($request->filled('PasswordKry')) {
            $user->PasswordKry = Hash::make($request->PasswordKry);
        }
        $user['updated_by'] = auth()->user()->id;

        $user->save();

        Alert::success('Berhasil', 'Data Pengguna Berhasil Diperbarui.');
        return redirect()->route('users.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();

        Alert::success('Berhasil', 'Data Pengguna Berhasil Dihapus.');
        return redirect()->route('users.index');
    }
}