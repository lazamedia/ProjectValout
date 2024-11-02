<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use ZipArchive;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;


class AdminDashboardController extends Controller
{
    /**
     * Display a listing of all projects with user details.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        // Mendapatkan semua proyek dengan informasi user
        $projects = Project::with('user')
            ->when($search, function ($query, $search) {
                return $query->where('name', 'LIKE', "%{$search}%")
                             ->orWhereHas('user', function ($query) use ($search) {
                                 $query->where('name', 'LIKE', "%{$search}%");
                             });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.dashboard', compact('projects', 'search'));
    }

    public function downloadAllProjects(Request $request)
{
    $date = $request->query('date'); // Tanggal yang dipilih oleh pengguna

    // Validasi apakah tanggal sudah dipilih
    if (!$date) {
        return response()->json(['error' => 'Tanggal harus dipilih.'], 400);
    }

    // Konversi tanggal ke format "d F Y" (misalnya, "2 November 2024")
    $formattedDate = Carbon::parse($date)->translatedFormat('j F Y');

    // Ambil semua proyek berdasarkan tanggal yang diformat
    $projects = Project::with('user', 'files')->where('tanggal', 'LIKE', "%{$formattedDate}%")->get();

    // Log hasil query untuk pengecekan
    Log::info("Jumlah proyek ditemukan pada tanggal $formattedDate: " . $projects->count());

    // Cek jika tidak ada proyek pada tanggal tersebut
    if ($projects->isEmpty()) {
        return response()->json(['error' => 'Tidak ada data pada tanggal tersebut.'], 404);
    }

    $zipFileName = 'projects_' . str_replace(' ', '_', $formattedDate) . '.zip';
    $zipFilePath = storage_path('app/temp/' . $zipFileName);

    // Buat direktori temp jika belum ada
    if (!file_exists(storage_path('app/temp'))) {
        mkdir(storage_path('app/temp'), 0755, true);
    }

    // Inisialisasi ZipArchive
    $zip = new ZipArchive();
    if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
        return response()->json(['error' => 'Gagal membuat file ZIP.'], 500);
    }

    // Tambahkan file proyek ke dalam ZIP
    foreach ($projects as $project) {
        $userFolder = $project->user->username . '_' . $project->name;

        foreach ($project->files as $file) {
            $filePath = storage_path('app/public/' . $file->file_path);
            if (file_exists($filePath)) {
                $relativeNameInZip = $userFolder . '/' . basename($filePath);
                $zip->addFile($filePath, $relativeNameInZip);
            }
        }
    }

    $zip->close();

    // Kembalikan file ZIP sebagai respons unduhan
    return response()->download($zipFilePath)->deleteFileAfterSend(true);
}



}
