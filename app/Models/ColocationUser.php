<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class ColocationUser extends Pivot
{
    protected $table = 'colocation_user';
    protected $fillable = ['left_at'];
    protected $casts = ['left_at' => 'datetime'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function colocation()
    {
        return $this->belongsTo(Colocation::class);
    }
}
