<?php

namespace App\Feed;

use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use SimpleXMLElement;
use Throwable;

final class CursorFeed
{
    public const CACHE_KEY = 'cursor-feed';

    /**
     * Fetch the feed, served from the application cache when available.
     *
     * @throws FeedUnavailableException
     */
    public function fetch(): Feed
    {
        return Cache::remember(self::CACHE_KEY, config('feeds.cache_ttl'), fn () => $this->load());
    }

    /**
     * @throws FeedUnavailableException
     */
    private function load(): Feed
    {
        try {
            $body = Http::timeout(10)
                ->retry(2, 500)
                ->get(config('feeds.url'))
                ->throw()
                ->body();
        } catch (Throwable $e) {
            throw new FeedUnavailableException('Could not fetch the feed.', previous: $e);
        }

        return $this->parse($body);
    }

    /**
     * @throws FeedUnavailableException
     */
    public function parse(string $xml): Feed
    {
        // The source emits whitespace before the XML declaration, which libxml
        // rejects. Also drop a UTF-8 BOM if one is present.
        $xml = ltrim($xml, " \t\r\n\xEF\xBB\xBF");

        $previous = libxml_use_internal_errors(true);

        try {
            $rss = simplexml_load_string($xml, SimpleXMLElement::class, LIBXML_NOCDATA | LIBXML_NONET);
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($previous);
        }

        if ($rss === false || ! isset($rss->channel)) {
            throw new FeedUnavailableException('The feed could not be parsed.');
        }

        $items = [];

        foreach ($rss->channel->item as $item) {
            $items[] = new FeedItem(
                title: trim((string) $item->title),
                link: trim((string) $item->link),
                publishedAt: $this->parseDate((string) $item->pubDate),
                content: $this->cleanContent((string) $item->description),
            );
        }

        return new Feed(
            title: trim((string) $rss->channel->title),
            link: trim((string) $rss->channel->link),
            items: $items,
        );
    }

    private function parseDate(string $value): ?CarbonImmutable
    {
        $value = trim($value);

        if ($value === '') {
            return null;
        }

        try {
            return CarbonImmutable::parse($value);
        } catch (Throwable) {
            return null;
        }
    }

    /**
     * The feed prefixes every description with the publication date followed
     * by a line break ("22 oktober 2026:<br/>"). Strip that prefix and any
     * remaining markup so only the plain summary text is left.
     */
    private function cleanContent(string $html): string
    {
        $html = preg_replace('/^\s*[^<]*?<br\s*\/?>\s*/i', '', $html, 1) ?? $html;

        $text = html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8');

        return trim(preg_replace('/\s+/u', ' ', $text) ?? $text);
    }
}
