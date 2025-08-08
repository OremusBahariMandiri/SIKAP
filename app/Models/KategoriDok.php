<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class KategoriDok extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'A04DmKategoriDok';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'IdKode',
        'KategoriDok',
        'created_by',
        'updated_by',
    ];

    /**
     * Generate ID Kode with format A040725001
     * Note: Fixed prefix from A03 to A04 to match table name
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
        $lastData = self::where('IdKode', 'like', "A04__{$year}%")
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

        return "A04{$month}{$year}{$formattedIncrement}";
    }

    /**
     * Get dokumen legal with this kategori
     */
    public function dokumenLegal()
    {
        return $this->hasMany(DokLegal::class, 'kategori_id');
    }
}