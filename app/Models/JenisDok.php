<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class JenisDok extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'A05DmJenisDok'; // Adjust if your table name is different

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'IdKode',
        'JenisDok',
        'idKategoriDok',
        'created_by',
        'updated_by',
    ];

    /**
     * Get the kategori associated with the document type.
     */
    public function kategori()
    {
        return $this->belongsTo(KategoriDok::class, 'idKategoriDok');
    }

    /**
     * Get dokumen legal with this jenis dokumen
     */
    public function dokumenLegal()
    {
        return $this->hasMany(DokLegal::class, 'jenis_id');
    }

    /**
     * Generate ID Kode with format A030725001
     *
     * @return string
     */
    public static function generateIdKode()
    {
        // Mendapatkan bulan dan tahun saat ini dalam format WIB
        $now = Carbon::now('Asia/Jakarta');
        $month = $now->format('m');
        $year = $now->format('y');

        // Ambil data terakhir dengan format bulan dan tahun yang sama
        $lastData = self::where('IdKode', 'like', "A03{$month}{$year}%")
            ->orderBy('IdKode', 'desc')
            ->first();

        if ($lastData) {
            // Ambil nomor increment dari ID terakhir
            $lastIncrement = (int) substr($lastData->IdKode, -3);
            $newIncrement = $lastIncrement + 1;
        } else {
            // Jika tidak ada data dengan bulan dan tahun yang sama, mulai dari 1
            $newIncrement = 1;
        }

        // Format increment menjadi 3 digit dengan leading zeros
        $formattedIncrement = str_pad($newIncrement, 3, '0', STR_PAD_LEFT);

        return "A03{$month}{$year}{$formattedIncrement}";
    }
}