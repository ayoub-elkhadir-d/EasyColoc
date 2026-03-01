<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Colocation extends Model
{
    protected $fillable = ['num', 'description', 'owner_id', 'status'];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function members()
    {
        return $this->belongsToMany(User::class)
            ->using(ColocationUser::class)
            ->withPivot('left_at')
            ->withTimestamps();
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

    public function cancel()
    {
        $this->update(['status' => 'cancelled']);
        $this->owner->decrementReputation(1);
    }

    public function isActive()
    {
        return $this->status === 'active';
    }

    public function calculateMemberDebt($userId)
    {
        $unpaidBalances = Balance::whereHas('depense', function($query) {
            $query->where('colocation_id', $this->id);
        })
        ->where('user_id', $userId)
        ->where('is_paid', false)
        ->sum('amount');

        return $unpaidBalances;
    }

    public function getSettlements()
    {
        $settlements = [];
        $members = $this->members;
        if (!$members->contains($this->owner_id)) {
            $members->push($this->owner);
        }

        foreach ($members as $member) {
            // Calculate what member paid (total of expenses they paid for)
            $paid = $this->depenses()->where('payer_id', $member->id)->sum('amount');

            // Calculate what member owes (only unpaid balances)
            $owed = Balance::whereHas('depense', function($query) {
                $query->where('colocation_id', $this->id);
            })
            ->where('user_id', $member->id)
            ->where('is_paid', false)
            ->sum('amount');

            $balance = $paid - $owed;
            
            $settlements[$member->id] = [
                'user' => $member,
                'balance' => $balance
            ];
        }

        return $settlements;
    }
}
