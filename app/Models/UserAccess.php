<?php

namespace App\Models;

use App\Traits\TracksChanges;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAccess extends Model
{
    use HasFactory, TracksChanges;

    protected $table = 'A02DmUserAccess';

    protected $fillable = [
        'IdKodeA01',
        'MenuAcs',
        'TambahAcs',
        'UbahAcs',
        'HapusAcs',
        'DownloadAcs',
        'DetailAcs',
        'MonitoringAcs',
        'created_by',
        'updated_by',
    ];

    /**
     * Cast attributes to their native types
     */
    protected $casts = [
        'TambahAcs' => 'boolean',
        'UbahAcs' => 'boolean',
        'HapusAcs' => 'boolean',
        'DownloadAcs' => 'boolean',
        'DetailAcs' => 'boolean',
        'MonitoringAcs' => 'boolean',
    ];

    /**
     * Get the user that owns the access.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'IdKodeA01', 'id');
    }

    /**
     * Override the create method to ensure proper boolean values
     */
    public static function create(array $attributes = [])
    {
        // Convert checkbox values to proper booleans
        $booleanFields = ['TambahAcs', 'UbahAcs', 'HapusAcs', 'DownloadAcs', 'DetailAcs', 'MonitoringAcs'];

        foreach ($booleanFields as $field) {
            if (isset($attributes[$field])) {
                $attributes[$field] = filter_var($attributes[$field], FILTER_VALIDATE_BOOLEAN);
            } else {
                $attributes[$field] = false;
            }
        }

        return parent::create($attributes);
    }
}