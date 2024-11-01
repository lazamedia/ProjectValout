<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class UserDashboardController extends Controller
{
    /**
     * Display a listing of the projects.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        // Query projects dengan pencarian jika ada
        $projects = Project::when($search, function ($query, $search) {
            return $query->where('name', 'LIKE', "%{$search}%");
        })->orderBy('created_at', 'desc')->paginate(10);

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
        // Validasi input
        $request->validate([
            'name' => 'required|string|max:255',
            'file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $filePath = null;

        // Debug: Log apakah file di-upload
        if ($request->hasFile('file')) {
            Log::info('File ditemukan pada request.');

            // Menggunakan storeAs untuk penamaan file yang unik
            $fileName = time() . '_' . $request->file('file')->getClientOriginalName();
            $filePath = $request->file('file')->storeAs('projects', $fileName, 'public');

            // Debug: Log path file yang disimpan
            Log::info('File disimpan di: ' . $filePath);
        } else {
            Log::info('Tidak ada file yang di-upload.');
        }

        // Simpan data proyek ke database
        Project::create([
            'name' => $request->name,
            'file_path' => $filePath,
        ]);

        return redirect()->route('user.index')->with('success', 'Project created successfully!');
    }

    /**
     * Show the form for editing the specified project.
     */
    public function edit(Project $project)
    {
        return view('user.edit', compact('project'));
    }

    /**
     * Update the specified project in storage.
     */
    public function update(Request $request, Project $project)
    {
        // Validasi input
        $request->validate([
            'name' => 'required|string|max:255',
            'file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        // Debug: Log sebelum update
        Log::info('Memulai proses update untuk proyek ID: ' . $project->id);

        // Handle file update jika ada
        if ($request->hasFile('file')) {
            Log::info('File baru ditemukan pada request.');

            // Hapus file lama jika ada
            if ($project->file_path && Storage::disk('public')->exists($project->file_path)) {
                Storage::disk('public')->delete($project->file_path);
                Log::info('File lama dihapus: ' . $project->file_path);
            }

            // Simpan file baru
            $fileName = time() . '_' . $request->file('file')->getClientOriginalName();
            $filePath = $request->file('file')->storeAs('projects', $fileName, 'public');
            $project->file_path = $filePath;

            // Debug: Log path file yang disimpan
            Log::info('File baru disimpan di: ' . $filePath);
        }

        // Update nama proyek
        $project->name = $request->name;
        $project->save();

        return redirect()->route('user.index')->with('success', 'Project updated successfully!');
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
    
        // Hapus file yang terkait (jika ada) dan hapus record dari database
        foreach ($request->ids as $id) {
            $project = Project::findOrFail($id);
            if ($project->file_path && file_exists(storage_path('app/' . $project->file_path))) {
                unlink(storage_path('app/' . $project->file_path));
            }
            $project->delete();
        }
    
        return redirect()->back()->with('success', 'Data berhasil dihapus.');
    }
    
}
