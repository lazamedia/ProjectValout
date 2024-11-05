<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserAbsensiController extends Controller
{
    /**
     * Menampilkan halaman data absensi pengguna.
     */
    public function index()
    {
        // Ambil data absensi berdasarkan pengguna yang sedang login
        $absensis = Absensi::where('user_id', Auth::id())->with('user')->get();

        return view('user.absen.absen-user', compact('absensis'));
    }

    /**
     * Memverifikasi kode absensi yang dimasukkan oleh pengguna.
     */
    public function checkKode(Request $request)
    {
        $request->validate([
            'kode_absen' => 'required|string',
        ]);

        // Cari room berdasarkan kode absensi yang dimasukkan
        $room = Room::where('kode_absen', $request->kode_absen)->first();

        if (!$room) {
            return redirect()->back()->with('error', 'Kode absensi tidak ditemukan.');
        }

        // Cek apakah waktu absensi masih terbuka
        $currentTime = now()->format('H:i:s');
        if ($currentTime < $room->jam_mulai || $currentTime > $room->jam_berakhir) {
            return redirect()->back()->with('error', 'Waktu absensi sudah ditutup/belom dibuka.');
        }

        // Cek apakah user sudah absen di room ini
        $alreadyAbsensi = Absensi::where('room_id', $room->id)
                                  ->where('user_id', Auth::id())
                                  ->exists();

        if ($alreadyAbsensi) {
            return redirect()->route('absenku.index')->with('error', 'Anda sudah melakukan absensi di room ini.');
        }

        // Simpan absensi
        Absensi::create([
            'room_id' => $room->id,
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('absenku.index')->with('success', 'Absensi berhasil disimpan.');
    }
}
