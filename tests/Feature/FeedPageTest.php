<?php

namespace Tests\Feature;

use App\Http\Controllers\FeedController;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class FeedPageTest extends TestCase
{
    private const FIRST_LINK = 'https://www.cursor.tue.nl/nieuws/2026/oktober/week-4/bakkie-040';

    private const SECOND_LINK = 'https://www.cursor.tue.nl/nieuws/2026/oktober/week-3/websites-weer-in-de-lucht';

    private function fakeFeed(): void
    {
        Http::fake([
            config('feeds.url') => Http::response(file_get_contents(base_path('tests/Fixtures/cursor-feed.xml'))),
        ]);
    }

    #[Test]
    public function it_renders_the_feed_in_dark_mode_by_default(): void
    {
        $this->fakeFeed();

        $response = $this->get('/');

        $response->assertOk()
            ->assertSee('bg-main-dark')
            ->assertSee('Bakkie 040 krijgt nieuw hart, maar blijft ontmoetingsplek')
            ->assertSee('Websites Cursor en TU/e weer in de lucht')
            ->assertSee('Artikel zonder datum')
            ->assertSee('Thursday 22nd of October 2026, 07:40:00')
            ->assertSee('Bakkie 040 is weer open.')
            ->assertDontSee('22 oktober 2026')
            ->assertSee(self::FIRST_LINK)
            ->assertSessionHas(FeedController::LAST_SEEN_SESSION_KEY, self::FIRST_LINK);
    }

    #[Test]
    public function all_items_are_marked_new_on_a_first_visit(): void
    {
        $this->fakeFeed();

        $this->get('/')->assertOk()->assertSeeInOrder([
            '&bull;</span> <a', 'Bakkie 040',
            '&bull;</span> <a', 'Websites Cursor',
            '&bull;</span> <a', 'Artikel zonder datum',
        ], false);
    }

    #[Test]
    public function items_from_the_last_seen_article_onwards_are_not_new(): void
    {
        $this->fakeFeed();

        $response = $this->withSession([FeedController::LAST_SEEN_SESSION_KEY => self::SECOND_LINK])->get('/');

        $response->assertOk();

        $html = $response->getContent();
        $this->assertSame(1, substr_count($html, '&bull;'), 'Only the first item should be marked as new.');
        $this->assertStringContainsString('&bull;</span> <a', $html);

        $response->assertSessionHas(FeedController::LAST_SEEN_SESSION_KEY, self::FIRST_LINK);
    }

    #[Test]
    public function light_mode_can_be_requested(): void
    {
        $this->fakeFeed();

        $this->get('/?light')
            ->assertOk()
            ->assertDontSee('bg-main-dark')
            ->assertSee('bg-gray-50');
    }

    #[Test]
    public function it_shows_a_notice_when_the_feed_is_unavailable(): void
    {
        Http::fake([config('feeds.url') => Http::response('', 503)]);

        $this->get('/')
            ->assertOk()
            ->assertSee('The feed is currently unavailable')
            ->assertSessionMissing(FeedController::LAST_SEEN_SESSION_KEY);
    }
}
