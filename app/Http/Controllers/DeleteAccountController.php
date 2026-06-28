<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DeleteAccountController extends Controller
{
    public function deleteAccount(){
        $user = Auth::user();
        $user->delete();

        return response(['user' => auth()->user(),  'success' => true, 'message' => 'Deleted Successfully']);
    }

    public function restoreAccount(){
        $user = Auth::user();
        $user->restore();

        return response(['user' => auth()->user(),  'success' => true, 'message' => 'Account Restored Successfully']);
    }
}
