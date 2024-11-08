<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role; // Import model Role
use Illuminate\Support\Facades\Validator;

class AdminUserController extends Controller
{
    /**
     * Tampilkan daftar semua pengguna dengan opsi pencarian.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */ 
    public function index(Request $request)
    {
        // Mengambil input pencarian dari request, jika ada
        $search = $request->input('search');

        // Membuat query builder untuk User dengan eager loading peran
        $query = User::with('roles');

        // Jika ada input pencarian, terapkan filter
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama', 'LIKE', '%' . $search . '%')
                  ->orWhere('username', 'LIKE', '%' . $search . '%');
            });
        }

        // Terapkan paginasi setelah menerapkan filter pencarian
        $users = $query->paginate(10)->appends(['search' => $search]);

        // Memuat semua peran untuk dropdown di SweetAlert
        $roles = Role::all();

        return view('admin.users', compact('users', 'roles', 'search'));
    }

    // ... metode lainnya tetap sama
}
