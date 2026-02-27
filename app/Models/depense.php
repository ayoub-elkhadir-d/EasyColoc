<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Depense extends Model
{
    protected $fillable = ['title', 'amount', 'date', 'colocation_id', 'payer_id', 'category_id'];

    public function colocation()
    {
        return $this->belongsTo(Colocation::class);
    }

    public function payer()
    {
        return $this->belongsTo(User::class, 'payer_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
