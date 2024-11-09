<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role; // Import model Role
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth; // Untuk otentikasi

class AdminUserController extends Controller
{
    /**
     * Tampilkan daftar semua pengguna.
     *
     * @return \Illuminate\Http\Response
     */ 
    public function index()
    {
        // Memuat peran pengguna menggunakan eager loading
        $users = User::with('roles')->paginate(10); // Atur jumlah per halaman sesuai kebutuhan
        $roles = Role::all(); // Memuat semua peran untuk dropdown di SweetAlert
        return view('admin.users', compact('users', 'roles'));
    }

    /**
     * Perbarui data pengguna tertentu.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $id,
            'role' => 'required|exists:roles,name', // Pastikan role ada
            'password' => 'nullable|string|min:6', // Password opsional, minimal 6 karakter jika diisi
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        // Cari pengguna
        $user = User::findOrFail($id);

        // Update data pengguna
        $user->nama = $request->nama;
        $user->username = $request->username;

        // Update password jika diisi
        if ($request->filled('password')) {
            $user->password = bcrypt($request->password); // Hash password sebelum disimpan
        }

        $user->save();

        // Sinkronkan peran pengguna
        $user->syncRoles([$request->role]);

        return response()->json([
            'success' => true,
            'message' => 'Data pengguna berhasil diperbarui.',
        ]);
    }

    /**
     * Hapus pengguna tertentu.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // Validasi apakah pengguna yang ingin dihapus adalah admin atau bukan
        $user = User::findOrFail($id);

        // Mencegah penghapusan diri sendiri
        if (Auth::id() === $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak dapat menghapus diri sendiri.',
            ], 403);
        }

        // Hapus pengguna
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'Pengguna berhasil dihapus.',
        ]);
    }
}
