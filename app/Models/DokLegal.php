<?php

namespace App\Models;

use App\Traits\TracksChanges;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class DokLegal extends Model
{
    use HasFactory, TracksChanges;

    protected $table = 'B01DokLegal';
    protected $fillable = [
        'IdKode',
        'NoRegDok',
        'DokPerusahaan',
        'perusahaan_id',
        'KategoriDok',
        'kategori_id',
        'JenisDok',
        'jenis_id',
        'PeruntukanDok',
        'DokAtasNama',
        'KetDok',
        'JnsMasaBerlaku',
        'TglTerbitDok',
        'TglBerakhirDok',
        'MasaBerlaku',
        'TglPengingat',
        'MasaPengingat',
        'FileDok',
        'StsBerlakuDok',
        'created_by',
        'updated_by',
    ];

    // Definisikan nilai default untuk FileDok
    protected $attributes = [
        'FileDok' => '',
    ];

    // Define date attributes to be cast
    protected $casts = [
        'TglTerbitDok' => 'date',
        'TglBerakhirDok' => 'date',
        'TglPengingat' => 'date',
    ];

    // Define date accessors for proper formatting
    protected $appends = ['tgl_terbit_formatted', 'tgl_berakhir_formatted', 'tgl_pengingat_formatted'];

    // Accessor for TglTerbitDok formatted
    public function getTglTerbitFormattedAttribute()
    {
        return $this->TglTerbitDok ? Carbon::parse($this->TglTerbitDok)->format('d/m/Y') : '-';
    }

    // Accessor for TglBerakhirDok formatted
    public function getTglBerakhirFormattedAttribute()
    {
        return $this->TglBerakhirDok ? Carbon::parse($this->TglBerakhirDok)->format('d/m/Y') : '-';
    }

    // Accessor for TglPengingat formatted
    public function getTglPengingatFormattedAttribute()
    {
        return $this->TglPengingat ? Carbon::parse($this->TglPengingat)->format('d/m/Y') : '-';
    }

    // Relasi ke Perusahaan
    public function perusahaan()
    {
        return $this->belongsTo(Perusahaan::class, 'perusahaan_id');
    }

    // Relasi ke Kategori
    public function kategori()
    {
        return $this->belongsTo(KategoriDok::class, 'kategori_id');
    }

    // Relasi ke Jenis
    public function jenis()
    {
        return $this->belongsTo(JenisDok::class, 'jenis_id');
    }

    // Generate ID Kode otomatis
    public static function generateIdKode()
    {
        // Mendapatkan bulan dan tahun saat ini dalam format WIB
        $now = Carbon::now('Asia/Jakarta');
        $month = $now->format('m');
        $year = $now->format('y');

        // Ambil data terakhir dengan format tahun yang sama (tidak peduli bulan)
        $lastData = self::where('IdKode', 'like', "B01__{$year}%")
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

        return "B01{$month}{$year}{$formattedIncrement}";
    }

    // Hitung masa berlaku dari dua tanggal
    public static function hitungMasaBerlaku($startDate, $endDate)
    {
        // Hitung selisih tahun, bulan, dan hari
        $years = $endDate->diffInYears($startDate);
        $months = $endDate->copy()->subYears($years)->diffInMonths($startDate);
        $days = $endDate->copy()->subYears($years)->subMonths($months)->diffInDays($startDate);

        $result = '';
        if ($years > 0) $result .= $years . ' thn ';
        if ($months > 0) $result .= $months . ' bln ';
        if ($days > 0) $result .= $days . ' hri';

        return $result ?: '0 hri';
    }

    // Mutator untuk memastikan TglBerakhirDok null jika JnsMasaBerlaku adalah Tetap
    public function setJnsMasaBerlakuAttribute($value)
    {
        $this->attributes['JnsMasaBerlaku'] = $value;

        if ($value === 'Tetap') {
            $this->attributes['TglBerakhirDok'] = null;
            $this->attributes['TglPengingat'] = null;
            $this->attributes['MasaBerlaku'] = 'Tetap';
            $this->attributes['MasaPengingat'] = '-';
        }
    }

}
