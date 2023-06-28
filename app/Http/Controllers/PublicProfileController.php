<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PublicProfileController extends Controller
{
    public function show(Request $request){
        return view('public_profile.show', [
            'user' => $request->user,
        ]);
    }
}
