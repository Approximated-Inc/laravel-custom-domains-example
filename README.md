# Laravel Custom Domains Example
This is an example repo to help you understand how you could implement custom domains easily as a feature in your Laravel app using (Approximated)[https://approximated.app].

## How it works
The example app is just a basic fresh Laravel app, with the Breeze auth starter added. It's setup to display a public user profile page on a custom domain.

When you register at /register, you'll be logged in and can enter a custom domain there. It'll create a virtual host in Approximated and provide the DNS info for pointing the domain.

The flow of a request:
- Hits web.php router 
- Hits a route group that matches the request domain with a primary domain pulled from your env
- Any requests with a domain that doesn't match will go to a second route group with a custom domains middleware
- If either the request header `apx-incoming-host` or the request host matches a custom domain in the database: 
  - it'll continue and merge in the custom domain and the matching user to the request
  - in this example we only have one route that goes to PublicProfileController's show method, and loads up the public profile
- If it doesn't match a custom domain in the database, or the primary domain, it 404s

## Files to check out
- `/routes/web.php` (for the route groups)
- `/app/Http/Middleware/CustomDomains.php` (to see how custom domains are matched to users)
- `/app/Http/Kernel.php` (aliased the middleware here)

## Assets and CORS
These work out of the box and required no changes in this example app, because they're all relatively pathed in the rendered html.

If your app is linking to assets with absolute paths/URLs, changing it to a relative path should fix any CORS issues.