<?php

namespace App\Http\Controllers;

use App\Models\DokLegal;
use App\Models\Perusahaan;
use App\Models\JenisDok;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CabangRekapController extends Controller
{
    public function index()
    {
        // Ambil jenis dokumen dengan ID 3
        $jenisDokumen = JenisDok::find(3);

        if (!$jenisDokumen) {
            return redirect()->back()->with('error', 'Jenis dokumen tidak ditemukan');
        }

        // Ambil semua data perusahaan
        $perusahaans = Perusahaan::orderBy('NamaPrsh')->get();

        // Ambil data rekap cabang per perusahaan
        $rekapCabang = [];
        $chartData = [];
        $colors = ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#5a5c69', '#858796', '#6610f2', '#6f42c1', '#e83e8c'];
        $colorIndex = 0;

        foreach ($perusahaans as $perusahaan) {
            // Hitung jumlah dokumen cabang yang berlaku untuk perusahaan ini
            $dokumenCabang = DokLegal::where('perusahaan_id', $perusahaan->id)
                ->where('jenis_id', 3)
                ->where('StsBerlakuDok', 'Berlaku')
                ->get();

            $jumlahCabang = count($dokumenCabang);

            // Simpan data untuk tabel (termasuk yang cabangnya 0)
            $rekapCabang[] = [
                'perusahaan_id' => $perusahaan->id,
                'nama_perusahaan' => $perusahaan->NamaPrsh,
                'jumlah_cabang' => $jumlahCabang,
                'dokumen_cabang' => $dokumenCabang,
            ];

            // Simpan data untuk chart hanya jika jumlah cabang > 0
            if ($jumlahCabang > 0) {
                $chartData[] = [
                    'label' => $perusahaan->NamaPrsh,
                    'value' => $jumlahCabang,
                    'color' => $colors[$colorIndex % count($colors)],
                ];

                $colorIndex++;
            }
        }

        // check if user is admin
        $isAdmin = auth()->user()->isAdmin();

        // If user is admin, grant all permissions
        if ($isAdmin) {
            $hasViewPermission = true;
            $hasExportPermission = true;
        } else {
            // Individual permission checks for non-admin users
            $hasViewPermission = auth()->user()->hasAccess('dokLegal', 'detail');
            $hasExportPermission = auth()->user()->hasAccess('dokLegal', 'download');
        }

        return view('cabang.rekap', compact(
            'rekapCabang',
            'chartData',
            'jenisDokumen',
            'hasViewPermission',
            'hasExportPermission',
            'isAdmin'
        ));
    }

    public function detail($perusahaanId)
    {
        // Ambil data perusahaan
        $perusahaan = Perusahaan::findOrFail($perusahaanId);

        // Ambil data cabang perusahaan yang berlaku (dokumen dengan jenis_id = 3 dan status Berlaku)
        $cabangDokumen = DokLegal::with(['perusahaan', 'kategori', 'jenis'])
            ->where('perusahaan_id', $perusahaanId)
            ->where('jenis_id', 3)
            ->where('StsBerlakuDok', 'Berlaku')
            ->get();

        // check if user is admin
        $isAdmin = auth()->user()->isAdmin();

        // If user is admin, grant all permissions
        if ($isAdmin) {
            $hasViewPermission = true;
            $hasExportPermission = true;
        } else {
            // Individual permission checks for non-admin users
            $hasViewPermission = auth()->user()->hasAccess('dokLegal', 'detail');
            $hasExportPermission = auth()->user()->hasAccess('dokLegal', 'download');
        }

        return view('cabang.show', compact(
            'perusahaan',
            'cabangDokumen',
            'hasViewPermission',
            'hasExportPermission',
            'isAdmin'
        ));
    }
}