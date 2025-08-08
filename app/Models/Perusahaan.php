<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Perusahaan extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'A03DmPerusahaan';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'IdKode',
        'NamaPrsh',
        'AlamatPrsh',
        'TelpPrsh',
        'EmailPrsh',
        'WebPrsh',
        'TglBerdiri',
        'TelpPrsh2',
        'EmailPrsh2',
        'BidangUsh',
        'IzinUsh',
        'GolonganUsh',
        'DirekturUtm',
        'Direktur',
        'KomisarisUtm',
        'Komisaris',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'TglBerdiri' => 'date',
    ];

    /**
     * Generate ID Kode with format A020725001
     *
     * @return string
     */
    public static function generateIdKode()
    {
        // Mendapatkan bulan dan tahun saat ini dalam format WIB
        $now = Carbon::now('Asia/Jakarta');
        $month = $now->format('m');
        $year = $now->format('y');

        // Ambil data terakhir dengan format tahun yang sama (tidak peduli bulan)
        $lastData = self::where('IdKode', 'like', "A03__{$year}%")
            ->orderBy('IdKode', 'desc')
            ->first();

        if ($lastData) {
            // Ambil nomor increment dari ID terakhir
            $lastIncrement = (int) substr($lastData->IdKode, -3);
            $newIncrement = $lastIncrement + 1;
        } else {
            // Jika tidak ada data dengan tahun yang sama, mulai dari 1
            $newIncrement = 1;
        }

        // Format increment menjadi 3 digit dengan leading zeros
        $formattedIncrement = str_pad($newIncrement, 3, '0', STR_PAD_LEFT);

        return "A03{$month}{$year}{$formattedIncrement}";
    }

    /**
     * Get dokumen legal related to this perusahaan
     */
    public function dokumenLegal()
    {
        return $this->hasMany(DokLegal::class, 'DokPerusahaan', 'NamaPrsh');
    }
}