<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    $feed = Feeds::make('https://www.cursor.tue.nl/rss.xml');
    //session(['last-seen-url' => '']);

    $lastSeen = session('last-seen-url', '');
    $new = true;

    $items = collect($feed->get_items())
        ->map(function ($item) use ($lastSeen, &$new) {
            //Title
            $title = $item->get_title();

            //Determine new status
            $link = $item->get_permalink();

            //Switch to false if entry is seen
            if ($link == $lastSeen) {
                $new = false;
            }

            //Get date and format it
            $pubdate = $item->get_item_tags('', 'pubDate')[0]['data'];
            $date = new Carbon\Carbon($pubdate);
            $date = $date->format('l jS \\of F Y, H:i:s');

            //Get content and strip date from it
            $unfiltered_content = $item->get_content();
            $content = str_ireplace(stristr($unfiltered_content, '<br>', true).'<br>', '', $unfiltered_content);

            //Return new entry
            return compact('title', 'link', 'date', 'content', 'new');
        });

    $data = array(
        'title'     => $feed->get_title(),
        'permalink' => $feed->get_permalink(),
        'items'     => $items->toArray(),
        'feed'      => $feed,
        'dark'      => ! request()->has('light'),
    );

    session([ 'last-seen-url' => ($items->first())['link'] ]);

    return view('cursor', $data);
});
