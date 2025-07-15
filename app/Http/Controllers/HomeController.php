<?php

namespace App\Http\Controllers;

use App\Models\DokLegal;
use App\Models\Perusahaan;
use App\Models\KategoriDok;
use App\Models\JenisDok;
use Illuminate\Http\Request;
use Carbon\Carbon;

class HomeController extends Controller
{
    /**
     * Display the home page as dashboard.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        // Statistik dokumen berdasarkan status
        $aktif = DokLegal::whereNotNull('TglBerakhirDok')
            ->where('TglBerakhirDok', '>', now())
            ->where('TglBerakhirDok', '>', now()->addDays(30))
            ->count();

        $hampirKedaluwarsa = DokLegal::whereNotNull('TglBerakhirDok')
            ->where('TglBerakhirDok', '>', now())
            ->where('TglBerakhirDok', '<=', now()->addDays(30))
            ->count();

        $kedaluwarsa = DokLegal::whereNotNull('TglBerakhirDok')
            ->where('TglBerakhirDok', '<=', now())
            ->count();

        $tetap = DokLegal::whereNull('TglBerakhirDok')
            ->orWhere('JnsMasaBerlaku', 'Tetap')
            ->count();

        $totalDokumen = DokLegal::count();

        // Dokumen terbaru
        $dokumenTerbaru = DokLegal::with(['perusahaan', 'kategori', 'jenis'])
            ->latest('TglTerbitDok')
            ->take(5)
            ->get();

        // Dokumen yang akan kedaluwarsa
        $dokumenHampirKedaluwarsa = DokLegal::with(['perusahaan', 'kategori', 'jenis'])
            ->whereNotNull('TglBerakhirDok')
            ->where('TglBerakhirDok', '>', now())
            ->where('TglBerakhirDok', '<=', now()->addDays(30))
            ->orderBy('TglBerakhirDok')
            ->take(3)
            ->get();

        // Statistik master data
        $totalPerusahaan = Perusahaan::count();
        $totalKategori = KategoriDok::count();
        $totalJenis = JenisDok::count();

        return view('home', compact(
            'aktif',
            'hampirKedaluwarsa',
            'kedaluwarsa',
            'tetap',
            'totalDokumen',
            'dokumenTerbaru',
            'dokumenHampirKedaluwarsa',
            'totalPerusahaan',
            'totalKategori',
            'totalJenis'
        ));
    }
}