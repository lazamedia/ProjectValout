<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Project extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'tanggal',
        'file_path',
        'user_id', // Tambahkan user_id di sini
    ];

    public function files()
    {
        return $this->hasMany(ProjectFile::class);
    }

    /**
     * Get the user that owns the project.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

//     protected static function boot()
// {
//     parent::boot();

//     static::deleting(function ($project) {
//         // Hapus file fisik
//         foreach ($project->files as $file) {
//             if (Storage::disk('public')->exists($file->file_path)) {
//                 Storage::disk('public')->delete($file->file_path);
//             }
//         }
//     });
// }


}
