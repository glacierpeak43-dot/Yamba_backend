<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ReferralController extends Controller
{
    public function requestMyReferralLink(Request $request){
        $user = Auth::user();
        if ($user->referral_code == null) {
            // Create a referral code and send it back
            $user->referral_code = Str::random(5);
            $user->save();  // Ensure the referral code is saved to the database
        }

        $link = url('/refer?ref=' . $user->referral_code);

        return $this->jsonSuccess(
            200,
            message: 'Referral code request successful',
            data: ['link'=> $link, 'code'=>  $user->referral_code],
            key: 'referral_code'
        );
    }
}
