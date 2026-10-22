# ListCursor.nl
Since the new cursor site, there is no nice listview.  
This is an alpha version to again have such view.

The app fetches the [cursor.tue.nl](https://www.cursor.tue.nl/) RSS feed, caches it for 10 minutes and renders it as a list. A PHP session (cookie) remembers the last article you saw so newer ones are marked with a bullet. Append `?light` to the URL for the light theme.

## Requirements
* PHP 8.5 or newer with the `curl`, `dom`, `mbstring`, `simplexml` and `xml` extensions
* Composer
* No database is needed: sessions and cache use the `file` driver

## Installation
1. Clone this repo.
2. Run `composer install --no-dev` (or `composer install` for development).
3. Run `cp .env.example .env && php artisan key:generate`.
4. Make sure `storage/` and `bootstrap/cache/` are writable by the web server.
5. Ready!
   * Serve locally using `php artisan serve`
   * Or set up a webserver with a default Laravel config (document root `public/`)

## Configuration
`FEED_URL` and `FEED_CACHE_TTL` (seconds) can be overridden in `.env`; see `config/feeds.php`.

## Development
The compiled CSS in `public/build/` is committed, so Node is only needed when changing styles:

```sh
npm install
npm run build   # or `npm run dev` while editing resources/css/app.css or the Blade view
```

Run the test suite and code style checks with:

```sh
composer test
vendor/bin/pint
```
