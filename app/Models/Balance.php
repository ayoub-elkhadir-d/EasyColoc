<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Balance extends Model
{
    protected $fillable = ['depense_id', 'user_id', 'amount', 'is_paid'];

    public function depense()
    {
        return $this->belongsTo(Depense::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
