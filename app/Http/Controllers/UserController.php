<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
class UserController extends Controller
{
 public function displayUsers(){
    $users = User::all();
    return view('/dashboard', compact('users'));
 }
    public function toggleBan(User $user)
{
    if($user->is_banned) {
        $user->unban();
    } else {
        $user->ban();
    }

    return redirect()->back();
}
}
