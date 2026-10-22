<?php

namespace App\Http\Controllers;

use App\Feed\CursorFeed;
use App\Feed\FeedUnavailableException;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FeedController extends Controller
{
    public const LAST_SEEN_SESSION_KEY = 'last-seen-url';

    public function __invoke(Request $request, CursorFeed $feed): View
    {
        $title = 'Cursor Listviewer';
        $permalink = null;
        $items = [];
        $unavailable = false;

        try {
            $result = $feed->fetch();
            $title = $result->title ?: $title;
            $permalink = $result->link ?: null;

            $lastSeen = $request->session()->get(self::LAST_SEEN_SESSION_KEY, '');
            $isNew = true;

            foreach ($result->items as $item) {
                // Everything from the last seen article onwards has already been read.
                if ($item->link === $lastSeen) {
                    $isNew = false;
                }

                $items[] = ['item' => $item, 'new' => $isNew];
            }

            if ($items !== []) {
                $request->session()->put(self::LAST_SEEN_SESSION_KEY, $items[0]['item']->link);
            }
        } catch (FeedUnavailableException $e) {
            Log::warning('Feed unavailable: '.$e->getMessage(), ['exception' => $e->getPrevious()]);
            $unavailable = true;
        }

        return view('cursor', [
            'title' => $title,
            'permalink' => $permalink,
            'items' => $items,
            'unavailable' => $unavailable,
            'dark' => ! $request->has('light'),
        ]);
    }
}
