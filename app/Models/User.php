<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_banned',
        'banned_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_banned' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function colocations()
    {
        return $this->belongsToMany(Colocation::class);
    }

    public function ownedColocations()
    {
        return $this->hasMany(Colocation::class, 'owner_id');
    }

    public function hasActiveMembership()
    {
        return $this->colocations()->exists() || $this->ownedColocations()->exists();
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class, 'payer_id');
    }

    /*
    |--------------------------------------------------------------------------
    | ROLE METHODS
    |--------------------------------------------------------------------------
    */

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isUser(): bool
    {
        return $this->role === 'user';
    }

    /*
    |--------------------------------------------------------------------------
    | BAN METHODS
    |--------------------------------------------------------------------------
    */

    public function ban(): void
    {
        $this->update([
            'is_banned' => true,
            'banned_at' => now()
        ]);
    }

    public function unban(): void
    {
        $this->update([
            'is_banned' => false,
            'banned_at' => null
        ]);
    }
}