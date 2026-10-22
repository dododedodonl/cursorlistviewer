<?php

namespace App\Feed;

use Carbon\CarbonImmutable;

final readonly class FeedItem
{
    public function __construct(
        public string $title,
        public string $link,
        public ?CarbonImmutable $publishedAt,
        public string $content,
    ) {}
}
