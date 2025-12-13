<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'nim',
        'nip',
        'judul_skripsi',
        'profile_picture',
        'prodi',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relasi untuk dosen
    public function mataKuliahs(): HasMany
    {
        return $this->hasMany(MataKuliah::class, 'dosen_id');
    }

    public function preferensiDosen(): HasMany
    {
        return $this->hasMany(PreferensiDosen::class, 'dosen_id');
    }

    // Role checking methods
    public function isSuperAdmin(): bool
    {
        return $this->role === 'admin_super';
    }

    public function isAdminProdi(): bool
    {
        return $this->role === 'admin_prodi';
    }

    public function isDosen(): bool
    {
        return $this->role === 'dosen';
    }

    public function isAdmin(): bool
    {
        return $this->isSuperAdmin() || $this->isAdminProdi();
    }
}
