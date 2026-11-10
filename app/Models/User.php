<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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
    

    protected $primaryKey = 'id_user';
    const UPDATED_AT = null;            
    protected $fillable = [
        'id_role',
        'nama',
        'email',
        'password',
        'no_hp',
        'alamat'];

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
     public function role()
    {
        return $this->belongsTo(Role::class, 'id_role', 'id_role');
    }
    public function punyaRole(string ...$roles): bool
    {
        return in_array($this->role?->nama_role, $roles);
    }
}
