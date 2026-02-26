<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Invitation extends Model
{
    protected $fillable = ['colocation_id', 'email', 'token', 'accepted'];

    public static function boot()
    {
        parent::boot();
        static::creating(function ($invitation) {
            $invitation->token = Str::random(32);
        });
    }

    public function colocation()
    {
        return $this->belongsTo(Colocation::class);
    }
}
