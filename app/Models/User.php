<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    // Jika tabel Anda memiliki nama khusus
    protected $table = 'A01DmUser';

    // Jika primary key bukan 'id'
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'IdKode',
        'NikKry',
        'NamaKry',
        'DepartemenKry',
        'JabatanKry',
        'WilkerKry',
        'PasswordKry',
        'is_admin',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'PasswordKry',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'PasswordKry' => 'hashed',
        'is_admin' => 'boolean',
    ];

    /**
     * Get the name of the unique identifier for the user.
     *
     * @return string
     */
    public function getAuthIdentifierName()
    {
        return 'NikKry';
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->PasswordKry;
    }

    /**
     * Get the name of the user.
     *
     * @return string
     */
    public function getName()
    {
        return $this->NamaKry;
    }

    /**
     * Generate unique ID kode
     *
     * @return string
     */
    public static function generateIdKode()
    {
        $lastUser = self::orderBy('id', 'desc')->first();

        if (!$lastUser) {
            return 'USR0001';
        }

        $lastId = $lastUser->IdKode;
        $lastNumber = intval(substr($lastId, 3));
        $newNumber = $lastNumber + 1;

        return 'USR' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get the access permissions for the user.
     */
    public function accessPermissions()
    {
        return $this->hasMany(UserAccess::class, 'IdKodeA01', 'id');
    }

    /**
     * Check if the user is an admin.
     *
     * @return bool
     */
    public function isAdmin()
    {
        return (bool) $this->is_admin;
    }

    /**
     * Check if the user has access to a specific action on a menu.
     *
     * @param string $menu
     * @param string $action (tambah, ubah, hapus, download, detail, monitoring)
     * @return bool
     */
    public function hasAccess($menu, $action)
    {
        // Admin has all permissions
        if ($this->isAdmin()) {
            return true;
        }

        // Skip access check if no admins exist yet (first user setup)
        $adminsCount = self::where('is_admin', true)->count();
        if ($adminsCount == 0) {
            return true;
        }

        // Eager load access permissions if not already loaded
        if (!$this->relationLoaded('accessPermissions')) {
            $this->load('accessPermissions');
        }

        // Find the access record for this menu
        $access = $this->accessPermissions->where('MenuAcs', $menu)->first();

        if (!$access) {
            return false;
        }

        // Check specific permission based on action
        switch ($action) {
            case 'tambah':
                return (bool) $access->TambahAcs;
            case 'ubah':
                return (bool) $access->UbahAcs;
            case 'hapus':
                return (bool) $access->HapusAcs;
            case 'download':
                return (bool) $access->DownloadAcs;
            case 'detail':
                return (bool) $access->DetailAcs;
            case 'monitoring':
                return (bool) $access->MonitoringAcs;
            default:
                return false;
        }
    }
    
}