<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Colocation extends Model
{
    protected $fillable = ['num', 'description', 'owner_id'];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function members()
    {
        return $this->belongsToMany(User::class);
    }

    public function invitations()
    {
        return $this->hasMany(Invitation::class);
    }

    public function depenses()
    {
        return $this->hasMany(Depense::class);
    }

    public function categories()
    {
        return $this->hasMany(Category::class);
    }
}
