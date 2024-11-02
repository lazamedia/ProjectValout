<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use ZipArchive;


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

    public function downloadAllProjects()
    {
        $zipFileName = 'all_projects.zip';
        $zipFilePath = storage_path('app/temp/' . $zipFileName);

        // Buat direktori temp jika belum ada
        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }

        // Inisialisasi ZipArchive
        $zip = new ZipArchive();
        if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
            return redirect()->back()->with('error', 'Gagal membuat file ZIP.');
        }

        // Ambil semua proyek dengan informasi pengguna
        $projects = Project::with('user', 'files')->get();

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

        // Download file ZIP dan hapus setelah dikirim
        return response()->download($zipFilePath)->deleteFileAfterSend(true);
    }


}
