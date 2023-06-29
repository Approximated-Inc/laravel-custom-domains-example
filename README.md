# Laravel Custom Domains Example
This is an example repo to help you understand how you could implement custom domains easily as a feature in your Laravel app using [Approximated](https://approximated.app).

## How it works
The example app is just a basic fresh Laravel app, with the Breeze auth starter added. 

The example purpose of this app is to display a public user profile page on a custom domain.

When you register at /register, you'll be logged in and can enter a custom domain there. 

On save it'll create a virtual host in Approximated and provide the DNS info for pointing the domain. On change it'll update the virtual host or delete it (if cleared).

The flow of a request in this app:
- Hits web.php router 
- Hits a route group that matches the request domain with a primary domain pulled from your env
- Any requests with a domain that doesn't match will go to a second route group with a custom domains middleware
- If either the request header `apx-incoming-host` or the request host matches a custom domain in the database: 
  - it'll continue and merge in the custom domain and the matching user to the request
  - in this example we only have one route that goes to PublicProfileController's show method, and loads up the public profile
- If it doesn't match a custom domain in the database, or the primary domain, it 404s

## Trying it out
This assumes that you have NPM installed, and the usual things for a laravel install (php, composer, etc.)

1. Copy .env.example to .env
2. Set the APP_PRIMARY_DOMAIN in .env to localhost or whatever you're putting in your browser to get to your dev env
3. Set APPROXIMATED_API_KEY in .env to your API key from the Approximated dashboard 
4. If you don't have SSL for your dev env, remove GEN_HTTPS_URLS from .env
5. Run `npm run dev` and `php artisan serve` from the project root folder 
6. Open your browser to the dev env and go to /register to create an acount
7. After account creation you'll be logged in to a dashboard with a form. 
8. In the custom domain field, enter the domain of your dev env (localhost, for instance) and click save.
9. To test the custom domain in a local environment: 
  - Change the APP_PRIMARY_DOMAIN in .env to anything else temporarily, then reload the page.
  - This makes the router not match that as the primary domain, so it'll attempt it as a custom domain.
  - You should see a public profile for that user with a few example pages (it's pretty basic, just an example)
  - To get back to the main app dashboard again, just change the APP_PRIMARY_DOMAIN back.

## Files to check out
- [routes/web.php](routes/web.php) - for the route groups
- [app/Http/Middleware/CustomDomains.php](app/Http/Middleware/CustomDomains.php) - to see how custom domains are matched to users
- [app/Http/Kernel.php](app/Http/Kernel.php) - aliased the middleware here
- [app/Http/Controllers/PublicProfileController.php](app/Http/Controllers/PublicProfileController.php) - loads up a public profile for this example
- [resources/views/public_profile/show.blade.php](resources/views/public_profile/show.blade.php) - view that extends up separate layout from default
- [resources/views/layouts/public_profile_layout.blade.php](resources/views/layouts/public_profile_layout.blade.php) - a separate layout for custom domains

## Assets and CORS
These work out of the box and required no changes in this example app, because they're all relatively pathed in the rendered html.

If your app is linking to assets with absolute paths/URLs, changing it to a relative path should fix any CORS issues.

## Digging Deeper
The key parts of this are the router and the custom domains middleware.

*The router* has two route groups, one for requests matching the app's primary domain, and one for any other domains.

*The custom domains middleware* is only applied to requests that make it to that second route group. 
It checks if the hostname or a header `apx-incoming-host` matches a user's custom domain. If it does, it loads the public profile by default.

The public profile controller is just a regular controller, and the view is just a regular view. The only difference is that the view extends a different layout from the rest of the app for this example.