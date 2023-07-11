<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use App\Services\Approximated;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    public function show(Request $request): View
    {
        return view('profile.show', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $current_domain = $request->user()->custom_domain;
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $apx = new Approximated;

        // These nested ifs below are gross but easier to understand for the example
        
        // did the custom domain field change?
        if($request->filled('custom_domain')){
            // was there a current domain to update in our DB? 
            // If yes, we want to update on Approximated, not create.
            if ($current_domain) {
                // double check the vhost exists on Approximated
                $vhost_check = $apx->get_vhost($current_domain);
                if($vhost_check['success']){
                    // It exists, update it
                    $apx->update_vhost($current_domain, ['incoming_address' => $request->input('custom_domain')]);
                }else{
                    // It doesn't exist, create it
                    $apx->create_vhost($request->user()->incoming_address, env('APP_PRIMARY_DOMAIN'));
                }
            }else{
                // No previous custom domain, create one
                $apx->create_vhost($request->user()->incoming_address, env('APP_PRIMARY_DOMAIN'));
            }
        }elseif($current_domain){
            // They've blanked the custom domain, and there was one previously.
            // Delete the vhost on Approximated.
            $apx->delete_vhost($current_domain);
        }

        $request->user()->save();


        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
