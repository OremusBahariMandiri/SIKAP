<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserAccess;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Log;

class UserAccessController extends Controller
{
    /**
     * Show the form for editing user access permissions.
     */
    public function edit(User $user)
    {
        // Debug info
        Log::debug('UserAccessController::edit', [
            'user_id' => $user->id,
            'username' => $user->NamaKry,
            'is_admin' => $user->isAdmin()
        ]);

        // Define all available menus
        $availableMenus = $this->getAvailableMenus();

        // Get current user access permissions
        $userAccess = $user->accessPermissions()->get()->keyBy('MenuAcs')->toArray();

        // Debug user access
        Log::debug('Current user access', [
            'user_id' => $user->id,
            'access_count' => count($userAccess),
            'access_data' => $userAccess
        ]);

        return view('users.access', compact('user', 'availableMenus', 'userAccess'));
    }

    /**
     * Update the user access permissions.
     */
    public function update(Request $request, User $user)
    {
        // Debug received data
        Log::debug('UserAccessController::update - Request Data', [
            'user_id' => $user->id,
            'is_admin_checkbox' => $request->has('is_admin'),
            'access_data' => $request->input('access')
        ]);

        $validatedData = $request->validate([
            'access' => 'required|array',
            'access.*.MenuAcs' => 'required|string',
            'access.*.TambahAcs' => 'boolean',
            'access.*.UbahAcs' => 'boolean',
            'access.*.HapusAcs' => 'boolean',
            'access.*.DownloadAcs' => 'boolean',
            'access.*.DetailAcs' => 'boolean',
            'access.*.MonitoringAcs' => 'boolean',
        ]);

        // Delete all existing access permissions for the user
        $deleteResult = $user->accessPermissions()->delete();
        Log::debug('Deleted existing permissions', ['result' => $deleteResult]);

        // Create new access permissions based on the form data
        $createdPermissions = [];
        foreach ($validatedData['access'] as $accessData) {
            $permission = $user->accessPermissions()->create([
                'MenuAcs' => $accessData['MenuAcs'],
                'TambahAcs' => isset($accessData['TambahAcs']),
                'UbahAcs' => isset($accessData['UbahAcs']),
                'HapusAcs' => isset($accessData['HapusAcs']),
                'DownloadAcs' => isset($accessData['DownloadAcs']),
                'DetailAcs' => isset($accessData['DetailAcs']),
                'MonitoringAcs' => isset($accessData['MonitoringAcs']),
                'created_by' => auth()->user()->id,
                'updated_by' => auth()->user()->id,
            ]);

            $createdPermissions[] = $permission->toArray();
        }

        Log::debug('Created new permissions', ['permissions' => $createdPermissions]);

        // Update admin status if provided
        $adminBefore = $user->is_admin;
        if ($request->has('is_admin')) {
            $user->is_admin = true;
        } else {
            $user->is_admin = false;
        }

        // Set updated_by for user table
        $user->updated_by = auth()->user()->id;

        // Save changes
        $saveResult = $user->save();

        Log::debug('Updated user admin status', [
            'before' => $adminBefore,
            'after' => $user->is_admin,
            'save_result' => $saveResult
        ]);

        // Reload user to verify changes
        $refreshedUser = User::find($user->id);
        $refreshedAccess = $refreshedUser->accessPermissions()->get()->toArray();

        Log::debug('Verification after update', [
            'user_admin_status' => $refreshedUser->is_admin,
            'access_count' => count($refreshedAccess),
            'access_data' => $refreshedAccess
        ]);

        Alert::success('Berhasil', 'Hak Akses Pengguna Berhasil Diperbarui.');
        return redirect()->route('users.index');
    }

    /**
     * Get all available menus in the application.
     * This returns all the controllers/modules that we want to manage access for.
     */
    private function getAvailableMenus()
    {
        return [
            [
                'name' => 'users',
                'display_name' => 'Manajemen Pengguna',
                'controller' => 'UserController',
            ],
            [
                'name' => 'perusahaan',
                'display_name' => 'Data Perusahaan',
                'controller' => 'PerusahaanController',
            ],
            [
                'name' => 'kategori-dok',
                'display_name' => 'Kategori Dokumen',
                'controller' => 'KategoriDokController',
            ],
            [
                'name' => 'jenis-dok',
                'display_name' => 'Jenis Dokumen',
                'controller' => 'JenisDokController',
            ],
            [
                'name' => 'dokLegal',
                'display_name' => 'Dokumen Legal',
                'controller' => 'DokLegalController',
            ],
        ];
    }
}
