<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Absensi;
use App\Models\Room;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AbsensiController extends Controller
{

    public function createAbsensi($kode)
    {
        $room = Room::where('kode_absen', $kode)->firstOrFail();
    
        // Cek apakah waktu absensi masih terbuka
        $currentTime = now()->format('H:i:s');
        if ($currentTime < $room->jam_mulai || $currentTime > $room->jam_berakhir) {
            return redirect()->back()->with('error', 'Waktu absensi sudah ditutup.');
        }
    
        // Cek apakah user sudah absen
        $alreadyAbsensi = Absensi::where('room_id', $room->id)
                                  ->where('user_id', Auth::id())
                                  ->exists();
    
        if ($alreadyAbsensi) {
            return redirect()->back()->with('error', 'Anda sudah melakukan absensi.');
        }
    
        // Ambil total data absensi di room ini
        $totalAbsensi = Absensi::where('room_id', $room->id)->count();
    
        return view('absensi.mandiri', compact('room', 'totalAbsensi'));
    }
    public function getTotalAbsensi()
    {
        $totalAbsensi = Absensi::count();
        return view('absensi.absen', compact('totalAbsensi')); // Misalnya ditampilkan di halaman utama absensi
    }
        
    // Menyimpan absensi mandiri
    public function store(Request $request, $kode)
    {
        $room = Room::where('kode_absen', $kode)->firstOrFail();

        // Validasi
        $request->validate([
            'nama' => 'required|string|max:255',
            'nim' => 'required|string|max:255',
            'kelas' => 'required|string|max:255',
        ]);

        // Cek apakah user sudah absen
        $alreadyAbsensi = Absensi::where('room_id', $room->id)
                                  ->where('user_id', Auth::id())
                                  ->exists();

        if ($alreadyAbsensi) {
            return redirect()->back()->with('error', 'Anda sudah melakukan absensi.');
        }

        // Simpan absensi
        Absensi::create([
            'room_id' => $room->id,
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('absensi.createAbsensi', $kode)->with('success', 'Absensi berhasil disimpan');
    }

    // Menambahkan absensi berdasarkan NIM (untuk admin)
    public function addByNim(Request $request, $room_id)
    {
        $request->validate([
            'nim' => 'required|string|exists:users,nim',
        ], [
            'nim.exists' => 'NIM tidak ditemukan.',
        ]);

        $user = User::where('nim', $request->nim)->first();

        if (!$user) {
            return redirect()->back()->with('error', 'User dengan NIM tersebut tidak ditemukan.');
        }

        // Cek apakah user sudah absen di room ini
        $alreadyAbsensi = Absensi::where('room_id', $room_id)
                                  ->where('user_id', $user->id)
                                  ->exists();

        if ($alreadyAbsensi) {
            return redirect()->back()->with('error', 'User sudah melakukan absensi di room ini.');
        }

        // Tambahkan absensi
        Absensi::create([
            'room_id' => $room_id,
            'user_id' => $user->id,
        ]);

        return redirect()->back()->with('success', 'Absensi berhasil ditambahkan untuk ' . $user->name);
    }

    public function destroy($id)
    {
        $absensi = Absensi::findOrFail($id);

        // Hapus data absensi
        $absensi->delete();

        return redirect()->back()->with('success', 'Absensi berhasil dihapus.');
    }


}
