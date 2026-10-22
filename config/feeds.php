<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Feed URL
    |--------------------------------------------------------------------------
    |
    | The RSS 2.0 feed that is fetched and rendered as a list view.
    |
    */

    'url' => env('FEED_URL', 'https://www.cursor.tue.nl/rss.xml'),

    /*
    |--------------------------------------------------------------------------
    | Cache TTL
    |--------------------------------------------------------------------------
    |
    | Number of seconds the parsed feed is kept in the application cache
    | before it is fetched again from the source.
    |
    */

    'cache_ttl' => (int) env('FEED_CACHE_TTL', 600),

];
