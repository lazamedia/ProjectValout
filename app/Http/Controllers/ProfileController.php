<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{

    public function edit()
    {
        $user = Auth::user();
        return view('user.profile.index', compact('user')); // Pastikan nama view sesuai
    }

    /**
     * Proses pembaruan profil pengguna.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('profile.edit')->with('error', 'Pengguna tidak ditemukan.');
        }

        // Validasi input
        $request->validate([
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'nama' => 'required|string|max:255',
            'nim' => 'required|string|max:255',
            'kelas' => 'required|string|max:255',
            'password' => 'nullable|string|min:8|confirmed', // password_confirmation
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'delete_image' => 'nullable|boolean',
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

        // Cek dan update NIM jika berubah
        if ($user->nim !== $request->nim) {
            $user->nim = $request->nim;
            $updated = true;
        }

        // Cek dan update Kelas jika berubah
        if ($user->kelas !== $request->kelas) {
            $user->kelas = $request->kelas;
            $updated = true;
        }

        // Cek dan update password jika diisi
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
            $updated = true;
        }

        // Cek dan hapus gambar profil jika diminta
        if ($request->filled('delete_image') && $request->delete_image) {
            if ($user->image && Storage::disk('public')->exists($user->image)) {
                Storage::disk('public')->delete($user->image);
                $user->image = null;
                $updated = true;
            }
        }

        // Cek dan upload gambar profil baru jika diupload
        if ($request->hasFile('image')) {
            // Hapus gambar lama jika ada
            if ($user->image && Storage::disk('public')->exists($user->image)) {
                Storage::disk('public')->delete($user->image);
            }

            // Simpan gambar baru
            $path = $request->file('image')->store('images', 'public');
            $user->image = $path;
            $updated = true;
        }

        if ($updated) {
            $user->save(); // Pastikan $user adalah instance Eloquent dan memiliki metode save()
            return redirect()->route('profile.edit')->with('success', 'Profil berhasil diperbarui.');
        }

        return redirect()->route('profile.edit')->with('success', 'Tidak ada perubahan yang dilakukan.');
    }
}
