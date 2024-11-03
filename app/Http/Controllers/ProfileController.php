<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    /**
     * Tampilkan form edit profil.
     */
    public function edit()
    {
        $user = Auth::user();
        return view('user.profile.index', compact('user'));
    }

    /**
     * Proses pembaruan profil pengguna.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        // Validasi input
        $request->validate([
            'username' => 'string|max:255|unique:users,username,' . $user->id,
            'nama' => 'string|max:255',
            'password' => 'nullable|string|min:8|confirmed', // password_confirmation
        ]);

        $updated = false; // Flag untuk cek apakah ada perubahan

        // Cek dan update username jika berubah
        if ($user->username !== $request->username) {
            $user->username = $request->username;
            $updated = true;
        }

        // Cek dan update nama jika berubah
        if ($user->nama !== $request->nama) {
            $user->nama = $request->nama;
            $updated = true;
        }

        // Cek dan update password jika diisi
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
            $updated = true;
        }

        if ($updated) {
            $user->save();
            return redirect()->route('profile.edit')->with('success', 'Profil berhasil diperbarui.');
        }

        return redirect()->route('profile.edit')->with('success', 'Tidak ada perubahan yang dilakukan.');
    }
}
