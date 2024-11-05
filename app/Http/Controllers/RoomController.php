<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use Illuminate\Http\Request;
use App\Models\Room;
use Illuminate\Support\Str;


class RoomController extends Controller
{
    // Middleware untuk autentikasi dan otorisasi (hanya admin)


    public function index()
    {
        $rooms = Room::all();
        $rooms = Room::withCount('absensis')->get();
        return view('absensi.index', compact('rooms'));

    }

    public function create()
    {
        return view('absensi.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_room' => 'required|string|max:255',
            'tema' => 'required|string|max:255',
            // 'kode_absen' => 'required|string|unique:rooms,kode_absen', // Tidak diperlukan jika otomatis
            'tanggal' => 'required|date',
            'jam_mulai' => 'required',
            'jam_berakhir' => 'required|after:jam_mulai',
        ]);
    
        // Generate kode absen unik
        $validated['kode_absen'] = strtoupper(Str::random(6));
    
        Room::create($validated);
        return redirect()->route('rooms.index')->with('success', 'Room berhasil dibuat');
    }
    

    public function show(Room $room)
    {
        $room->load('absensis.user');
        return view('absensi.absen', compact('room'));
    }

    public function edit(Room $room)
    {
        return view('absensi.edit', compact('room'));
    }

    public function update(Request $request, Room $room)
    {
        $validated = $request->validate([
            'nama_room' => 'required|string|max:255',
            'tema' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'jam_mulai' => 'required',
            'jam_berakhir' => 'required|after:jam_mulai',
        ]);

        $room->update($validated);
        return redirect()->route('rooms.index')->with('success', 'Room berhasil diupdate');
    }

    public function destroy(Room $room)
    {
        $room->delete();
        return redirect()->route('rooms.index')->with('success', 'Room berhasil dihapus');
    }
}
