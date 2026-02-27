<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class ColocationUser extends Pivot
{
    protected $table = 'colocation_user';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function colocation()
    {
        return $this->belongsTo(Colocation::class);
    }
}
