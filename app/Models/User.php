<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles; // Tambahkan ini
use Carbon\Carbon;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles; // Tambahkan HasRoles di sini

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama',
        'username',
        'password',
        'nim',
        'kelas',
        'image',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $dates = [
        'last_login_at',
        // kolom lainnya
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime', // Menambahkan ini
    ];
    
    
    // Relasi dengan Absensi
    public function absensis()
    {
        return $this->hasMany(Absensi::class);
    }

    public function getStatusAttribute()
{
    if ($this->last_login_at) {
        $now = Carbon::now();
        $lastLogin = Carbon::parse($this->last_login_at);
        $diffInMinutes = $now->diffInMinutes($lastLogin);
        
        if ($diffInMinutes <= 5) {
            return '<span class="badge bg-success">Aktif</span>';
        } else {
            return '<span class="badge bg-warning">Aktif ' . $lastLogin->diffForHumans() . '</span>';
        }
    }
    
    return '<span class="badge bg-secondary">Tidak Diketahui</span>';
}

}
