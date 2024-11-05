<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role; // Import model Role
use Illuminate\Support\Facades\Validator;

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
        $user->save();

        // Sinkronkan peran pengguna
        $user->syncRoles([$request->role]);

        return response()->json([
            'success' => true,
            'message' => 'Data pengguna berhasil diperbarui.',
        ]);
    }
}

    