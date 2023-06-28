<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;

class CustomDomains
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $domain = $request->hasHeader('apx-incoming-host') ? $request->header('apx-incoming-host') : $request->host();
        $primaryDomain = env('APP_PRIMARY_DOMAIN');
        
        if($domain == $primaryDomain) {
            // This is a request to the primary domain, not a custom domain
            return $next($request);
        }

        $user = User::where('custom_domain', $domain)->firstOrFail();

        if (!$user) {
            abort(404);
        }

        // Append domain and user to the Request object
        // for easy retrieval in the application.
        
        $request->merge([
            'domain' => $domain,
            // In your own app, the user might be another entity instead, like a blog, store, etc.
            // Here it's a user because we're showing a public user profile on custom domains.
            'user' => $user
        ]);

        return $next($request);
    }
}