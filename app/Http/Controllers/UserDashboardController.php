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


class UserDashboardController extends Controller
{
    /**
     * Display a listing of the projects.
     */ 
    public function index(Request $request)
    {
        $search = $request->input('search');
        $user = Auth::user(); // Mendapatkan pengguna yang sedang diautentikasi

        // Query proyek yang dimiliki oleh pengguna yang sedang diautentikasi
        $projects = Project::where('user_id', $user->id)
            ->when($search, function ($query, $search) {
                return $query->where('name', 'LIKE', "%{$search}%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('user.index', compact('projects', 'search'));
    }

    /**
     * Show the form for creating a new project.
     */
    public function create()
    {
        return view('user.create');
    }

    /**
     * Store a newly created project in storage.
     */

     public function store(Request $request)
     {
        // dd($request->all(), $request->file('files'));
         // Validasi input
         $request->validate([
             'name' => 'required|string|max:255',
             'files' => 'required',
             'files.*' => 'file|max:2048',
         ]);
     
         // Pastikan pengguna diautentikasi
         $user = Auth::user();
         if (!$user) {
             return redirect()->back()->withErrors('Pengguna tidak diautentikasi.');
         }
     
         // Format tanggal dalam bahasa Indonesia
         $tanggalSekarang = Carbon::now()->locale('id')->isoFormat('D MMMM YYYY'); 
     
         // Buat proyek baru dengan format tanggal
         $project = Project::create([
             'name' => $request->name,
             'user_id' => $user->id,
             'tanggal' => $tanggalSekarang,
         ]);
     
         // Buat nama folder berdasarkan username, nama proyek, dan waktu pembuatan
         $folderName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $user->username . '_' . $request->name) . '_' . Carbon::now()->format('Ymd_His');
         $folderPath = 'projects/' . $folderName;
     
         // Simpan setiap file ke dalam folder proyek
         if ($request->hasFile('files')) {
             Log::info('Files ditemukan pada request.');
             
             foreach ($request->file('files') as $file) {
                 // Menggunakan nama asli file untuk penyimpanan di dalam folder proyek
                 $fileName = $file->getClientOriginalName();
                 $filePath = $file->storeAs($folderPath, $fileName, 'public');
     
                 // Log path file yang disimpan
                 Log::info('File disimpan di: ' . $filePath);
     
                 // Simpan data file ke tabel project_files
                 $project->files()->create([
                     'file_path' => $filePath,
                 ]);
             }
         } else {
             Log::info('Tidak ada file yang di-upload.');
         }
     
         return redirect()->route('user.index')->with('success', 'Project created successfully!');
     }
     
    
    
    

    /**
     * Show the form for editing the specified project.
     */
    public function edit(Project $project)
    {
        $user = Auth::user();
    
        // Tetap gunakan Gate tetapi izinkan admin tanpa cek Gate
        if ($user->role === 'admin' || Gate::allows('update', $project)) {
            return view('user.edit', compact('project'));
        }
    
        abort(403, 'Akses tidak diizinkan.');
    }
    
    

    /**
     * Update the specified project in storage.
     */
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
    
    return redirect()->route('user.index', $project->id)->with('success', 'Project updated successfully!');
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

        return redirect()->route('user.index')->with('success', 'Project deleted successfully!');
    }

    /**
     * Bulk delete projects.
     */
    public function bulkDelete(Request $request)
    {
        // Validasi apakah ID ada di request
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:projects,id', // validasi ID yang ada di database
        ]);
    
        foreach ($request->ids as $id) {
            $project = Project::findOrFail($id);
            if ($project) {
                // Menyimpan path folder proyek
                $folderPath = 'projects/' . preg_replace('/[^A-Za-z0-9_\-]/', '_', $project->user->username . '_' . $project->name) . '_' . Carbon::parse($project->created_at)->format('Ymd_His');
    
                // Hapus semua file terkait proyek
                foreach ($project->files as $file) {
                    // Hapus file dari storage
                    if (Storage::disk('public')->exists($file->file_path)) {
                        Storage::disk('public')->delete($file->file_path);
                    }
                    // Hapus record file dari database
                    $file->delete();
                }
    
                // Hapus folder proyek jika ada
                if (Storage::disk('public')->exists($folderPath)) {
                    Storage::disk('public')->deleteDirectory($folderPath);
                }
    
                // Hapus proyek dari database
                $project->delete();
            }
        }
    
        return redirect()->route('user.index')->with('success', 'Projects deleted successfully!');
    }
    



    public function download(Project $project)
    {
        $user = Auth::user();
    
        // Cek apakah pengguna adalah pemilik proyek atau admin
        if ($project->user_id !== $user->id && !$user->hasRole('admin')) {
            abort(403, 'Aksi tidak diizinkan.');
        }
    
        $files = $project->files;
    
        if ($files->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada file untuk proyek ini.');
        }
    
        // Sanitasi nama proyek untuk digunakan sebagai nama file ZIP
        $projectNameSanitized = preg_replace('/[^A-Za-z0-9_\-]/', '_', $project->name);
        $zipFileName = $projectNameSanitized . '.zip';
        $zipFilePath = storage_path('app/temp/' . $zipFileName);
    
        // Pastikan direktori temp ada
        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }
    
        // Inisialisasi ZipArchive
        $zip = new ZipArchive();
        if ($zip->open($zipFilePath, ZipArchive::CREATE) !== TRUE) {
            return redirect()->back()->with('error', 'Gagal membuat file ZIP.');
        }
    
        // Tambahkan setiap file ke dalam file ZIP
        foreach ($files as $file) {
            $filePath = storage_path('app/public/' . $file->file_path);
            if (file_exists($filePath)) {
                $relativeNameInZip = basename($filePath);
                $zip->addFile($filePath, $relativeNameInZip);
            }
        }
    
        $zip->close();
    
        // Mengembalikan file ZIP sebagai respons unduhan dengan nama sesuai proyek, dan menghapus file ZIP setelah dikirim
        return response()->download($zipFilePath, $zipFileName)->deleteFileAfterSend(true);
    }
    

    
}
