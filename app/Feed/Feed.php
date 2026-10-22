<?php

namespace App\Feed;

final readonly class Feed
{
    /**
     * @param  list<FeedItem>  $items
     */
    public function __construct(
        public string $title,
        public string $link,
        public array $items,
    ) {}
}
