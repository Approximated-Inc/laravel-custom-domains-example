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

    public function page(Request $request, $page){
        return view('public_profile.page', [
            'user' => $request->user,
            'page' => $page,
        ]);
    }
}
