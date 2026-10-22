<?php

namespace Tests\Unit;

use App\Feed\CursorFeed;
use App\Feed\FeedUnavailableException;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CursorFeedTest extends TestCase
{
    private function fakeFeed(string $xml): void
    {
        Http::fake([config('feeds.url') => Http::response($xml, 200, ['Content-Type' => 'application/rss+xml'])]);
    }

    private function fixture(): string
    {
        return file_get_contents(base_path('tests/Fixtures/cursor-feed.xml'));
    }

    #[Test]
    public function it_parses_the_channel_and_items_and_trims_whitespace(): void
    {
        $this->fakeFeed($this->fixture());

        $feed = app(CursorFeed::class)->fetch();

        $this->assertSame('Cursor - laatste nieuws', $feed->title);
        $this->assertSame('https://www.cursor.tue.nl/nieuws', $feed->link);
        $this->assertCount(3, $feed->items);

        $first = $feed->items[0];
        $this->assertSame('Bakkie 040 krijgt nieuw hart, maar blijft ontmoetingsplek', $first->title);
        $this->assertSame('https://www.cursor.tue.nl/nieuws/2026/oktober/week-4/bakkie-040', $first->link);
        $this->assertSame('2026-10-22T07:40:00+02:00', $first->publishedAt?->toIso8601String());
    }

    #[Test]
    public function it_strips_the_date_prefix_and_markup_from_the_content(): void
    {
        $this->fakeFeed($this->fixture());

        $items = app(CursorFeed::class)->fetch()->items;

        $this->assertSame(
            'Bakkie 040 is weer open. Na het faillissement in september hebben Jesse & Thijs de koffietent gekocht.',
            $items[0]->content,
        );
        $this->assertSame('De websites van Cursor en de TU/e zijn weer online.', $items[1]->content);
        $this->assertSame('Een samenvatting zonder datumprefix.', $items[2]->content);
    }

    #[Test]
    public function a_missing_publication_date_results_in_null(): void
    {
        $this->fakeFeed($this->fixture());

        $this->assertNull(app(CursorFeed::class)->fetch()->items[2]->publishedAt);
    }

    #[Test]
    public function it_caches_the_feed(): void
    {
        $this->fakeFeed($this->fixture());

        $service = app(CursorFeed::class);
        $service->fetch();
        $service->fetch();

        Http::assertSentCount(1);
    }

    #[Test]
    public function it_throws_when_the_feed_cannot_be_fetched(): void
    {
        Http::fake([config('feeds.url') => Http::response('Server error', 500)]);

        $this->expectException(FeedUnavailableException::class);

        app(CursorFeed::class)->fetch();
    }

    #[Test]
    public function it_throws_when_the_feed_is_not_valid_xml(): void
    {
        $this->fakeFeed('<html>not a feed');

        $this->expectException(FeedUnavailableException::class);

        app(CursorFeed::class)->fetch();
    }

    #[Test]
    public function it_throws_when_the_document_has_no_channel(): void
    {
        $this->fakeFeed('<?xml version="1.0"?><feed xmlns="http://www.w3.org/2005/Atom"><title>Atom</title></feed>');

        $this->expectException(FeedUnavailableException::class);

        app(CursorFeed::class)->fetch();
    }
}
