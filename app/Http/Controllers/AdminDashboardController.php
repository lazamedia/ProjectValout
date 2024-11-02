<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Gate;
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



    // App\Http\Controllers\AdminDashboardController.php

public function checkDownload(Request $request)
{
    $date = $request->query('date');

    // Validasi apakah tanggal sudah dipilih
    if (!$date) {
        return response()->json(['error' => 'Tanggal harus dipilih.'], 400);
    }

    // Set locale ke Indonesia
    Carbon::setLocale('id');

    try {
        // Konversi tanggal ke format "j F Y" (misalnya, "2 November 2024")
        $formattedDate = Carbon::parse($date)->translatedFormat('j F Y');
    } catch (\Exception $e) {
        Log::error('Kesalahan saat mem-parsing tanggal: ' . $e->getMessage());
        return response()->json(['error' => 'Format tanggal tidak valid.'], 400);
    }

    // Ambil semua proyek berdasarkan format tanggal yang sesuai
    $projects = Project::with('user', 'files')
        ->where('tanggal', 'LIKE', "%{$formattedDate}%")
        ->get();

    Log::info("Jumlah proyek ditemukan pada tanggal $formattedDate: " . $projects->count());

    if ($projects->isEmpty()) {
        return response()->json(['error' => 'Tidak ada data pada tanggal tersebut.'], 404);
    }

    return response()->json(['success' => true]);
}




    // App\Http\Controllers\AdminDashboardController.php

public function downloadAllProjects(Request $request)
{
    $date = $request->query('date');

    // Validasi apakah tanggal sudah dipilih
    if (!$date) {
        return redirect()->back()->with('error', 'Tanggal harus dipilih.');
    }

    // Set locale ke Indonesia
    Carbon::setLocale('id');

    try {
        // Konversi tanggal ke format "j F Y" (misalnya, "2 November 2024")
        $formattedDate = Carbon::parse($date)->translatedFormat('j F Y');
    } catch (\Exception $e) {
        Log::error('Kesalahan saat mem-parsing tanggal: ' . $e->getMessage());
        return redirect()->back()->with('error', 'Format tanggal tidak valid.');
    }

    // Ambil semua proyek berdasarkan format tanggal yang sesuai
    $projects = Project::with('user', 'files')
        ->where('tanggal', 'LIKE', "%{$formattedDate}%")
        ->get();

    Log::info("Jumlah proyek ditemukan pada tanggal $formattedDate: " . $projects->count());

    if ($projects->isEmpty()) {
        return redirect()->back()->with('error', 'Tidak ada data pada tanggal tersebut.');
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
        return redirect()->back()->with('error', 'Gagal membuat file ZIP.');
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
    return response()->download($zipFilePath, $zipFileName)->deleteFileAfterSend(true);
}






    public function edit(Project $project)
    {
        return view('user.edit', compact('project'));
    }

    public function update(Request $request, $id)
    {
        
        // Temukan project berdasarkan ID
        $project = Project::findOrFail($id);
        
        // Validasi input
        $request->validate([
            'name' => 'required|string|max:255',
            'files.*' => 'nullable|file|max:5120',
            'deleted_files.*' => 'nullable|integer|exists:project_files,id',
        ]);
        
        // Update nama proyek
        $project->name = $request->input('name');
        $project->save();
        
        // Pastikan pengguna yang sedang login
        $user = Auth::user();
        if (!$user) {
            return redirect()->back()->withErrors('Pengguna tidak diautentikasi.');
        }
        
        // Buat nama folder yang sama dengan format username_namaProject_waktu
        $folderName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $user->username . '_' . $project->name) . '_' . Carbon::parse($project->created_at)->format('Ymd_His');
        $folderPath = 'projects/' . $folderName;
        
        // Proses penghapusan file yang dipilih
        if ($request->has('deleted_files')) {
            $deletedFileIds = $request->input('deleted_files');
            foreach ($deletedFileIds as $fileId) {
                $file = ProjectFile::find($fileId);
                if ($file) {
                    // Hapus file dari storage
                    Storage::disk('public')->delete($file->file_path);
                    // Hapus record dari database
                    $file->delete();
                }
            }
        }
        
        // Proses upload file baru
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $uploadedFile) {
                // Menggunakan nama asli file untuk penyimpanan di dalam folder proyek
                $fileName = $uploadedFile->getClientOriginalName();
                $filePath = $uploadedFile->storeAs($folderPath, $fileName, 'public');
        
                // Simpan informasi file ke database
                $project->files()->create([
                    'file_path' => $filePath,
                    'mime_type' => $uploadedFile->getClientMimeType(),
                    'size' => $uploadedFile->getSize(),
                    'original_name' => $fileName,
                ]);
            }
        }
        
        return redirect()->route('admin.dashboard', $project->id)->with('success', 'Project updated successfully!');
    }
    
        
        
    
        /**
         * Remove the specified project from storage.
         */
        public function destroy(Project $project)
        {
            // Hapus file jika ada
            if ($project->file_path && Storage::disk('public')->exists($project->file_path)) {
                Storage::disk('public')->delete($project->file_path);
                Log::info('File dihapus saat menghapus proyek ID: ' . $project->id);
            }
    
            $project->delete();
    
            return redirect()->route('admin.dashboard')->with('success', 'Project deleted successfully!');
        }


}
