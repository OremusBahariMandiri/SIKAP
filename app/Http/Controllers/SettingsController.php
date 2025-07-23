<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class SettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display the settings page.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        return view('settings.index', compact('user'));
    }

    /**
     * Update the user's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:6|confirmed',
        ]);

        $user = Auth::user();

        // Check if current password is correct
        if (!Hash::check($request->current_password, $user->PasswordKry)) {
            return back()->withErrors(['current_password' => 'Password saat ini tidak sesuai.']);
        }

        // Update password
        $user->update([
            'PasswordKry' => Hash::make($request->password),
            'updated_by' => $user->id_kode,
        ]);

        return redirect()->route('settings.index')
            ->with('success', 'Password berhasil diperbarui.');
    }
}