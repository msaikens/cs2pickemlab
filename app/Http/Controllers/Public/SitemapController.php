<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Throwable;
use XMLWriter;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $urls = [];

        $this->addStaticUrls($urls);
        $this->addProducts($urls);
        $this->addMarketplaceListings($urls);
        $this->addTeams($urls);
        $this->addMatches($urls);
        $this->addPickemEvents($urls);

        ksort($urls);

        $xml = $this->buildXml($urls);

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
        ]);
    }

    private function addStaticUrls(array &$urls): void
    {
        $staticRoutes = [
            ['home', [], 'daily', '1.0'],
            ['marketplace.index', [], 'hourly', '0.95'],
            ['shop.index', [], 'daily', '0.90'],
            ['matches.index', [], 'daily', '0.85'],
            ['pickem.index', [], 'daily', '0.85'],
            ['teams.index', [], 'weekly', '0.75'],
            ['contact.create', [], 'monthly', '0.50'],
            ['legal.privacy', [], 'yearly', '0.30'],
            ['legal.terms', [], 'yearly', '0.30'],
            ['legal.data', [], 'yearly', '0.30'],
            ['legal.affiliate', [], 'yearly', '0.30'],
            ['legal.disclaimer', [], 'yearly', '0.30'],
            ['legal.law-enforcement', [], 'yearly', '0.40'],
            ['wallet.terms', [], 'yearly', '0.25'],
        ];

        foreach ($staticRoutes as [$routeName, $params, $changefreq, $priority]) {
            $url = $this->safeRoute($routeName, $params);

            if (! $url) {
                continue;
            }

            $this->addUrl(
                urls: $urls,
                loc: $url,
                lastmod: now(),
                changefreq: $changefreq,
                priority: $priority,
            );
        }
    }

    private function addProducts(array &$urls): void
    {
        if (! Schema::hasTable('products') || ! Route::has('shop.show')) {
            return;
        }

        $query = DB::table('products');

        if (Schema::hasColumn('products', 'status')) {
            $query->where('status', 'active');
        }

        if (! Schema::hasColumn('products', 'slug')) {
            return;
        }

        $query
            ->whereNotNull('slug')
            ->select(['slug', 'updated_at'])
            ->orderBy('id')
            ->chunkById(500, function ($products) use (&$urls) {
                foreach ($products as $product) {
                    $url = $this->safeRoute('shop.show', $product->slug);

                    if (! $url) {
                        continue;
                    }

                    $this->addUrl(
                        urls: $urls,
                        loc: $url,
                        lastmod: $product->updated_at ?? now(),
                        changefreq: 'weekly',
                        priority: '0.80',
                    );
                }
            });
    }

    private function addMarketplaceListings(array &$urls): void
    {
        if (! Schema::hasTable('skin_listings') || ! Route::has('marketplace.listings.show')) {
            return;
        }

        $query = DB::table('skin_listings');

        if (Schema::hasColumn('skin_listings', 'status')) {
            $query->where('status', 'active');
        }

        $query
            ->select(['id', 'updated_at'])
            ->orderBy('id')
            ->chunkById(500, function ($listings) use (&$urls) {
                foreach ($listings as $listing) {
                    $url = $this->safeRoute('marketplace.listings.show', $listing->id);

                    if (! $url) {
                        continue;
                    }

                    $this->addUrl(
                        urls: $urls,
                        loc: $url,
                        lastmod: $listing->updated_at ?? now(),
                        changefreq: 'daily',
                        priority: '0.85',
                    );
                }
            });
    }

    private function addTeams(array &$urls): void
    {
        if (! Schema::hasTable('teams') || ! Route::has('teams.show')) {
            return;
        }

        DB::table('teams')
            ->select(['id', 'updated_at'])
            ->orderBy('id')
            ->chunkById(500, function ($teams) use (&$urls) {
                foreach ($teams as $team) {
                    $url = $this->safeRoute('teams.show', $team->id);

                    if (! $url) {
                        continue;
                    }

                    $this->addUrl(
                        urls: $urls,
                        loc: $url,
                        lastmod: $team->updated_at ?? now(),
                        changefreq: 'weekly',
                        priority: '0.70',
                    );
                }
            });
    }

    private function addMatches(array &$urls): void
    {
        if (! Schema::hasTable('matches') || ! Route::has('matches.show')) {
            return;
        }

        DB::table('matches')
            ->select(['id', 'updated_at'])
            ->orderByDesc('id')
            ->limit(5000)
            ->get()
            ->each(function ($match) use (&$urls) {
                $url = $this->safeRoute('matches.show', $match->id);

                if (! $url) {
                    return;
                }

                $this->addUrl(
                    urls: $urls,
                    loc: $url,
                    lastmod: $match->updated_at ?? now(),
                    changefreq: 'daily',
                    priority: '0.75',
                );
            });
    }

    private function addPickemEvents(array &$urls): void
    {
        if (! Schema::hasTable('events') || ! Route::has('pickem.show')) {
            return;
        }

        DB::table('events')
            ->select(['id', 'updated_at'])
            ->orderByDesc('id')
            ->limit(1000)
            ->get()
            ->each(function ($event) use (&$urls) {
                $url = $this->safeRoute('pickem.show', $event->id);

                if (! $url) {
                    return;
                }

                $this->addUrl(
                    urls: $urls,
                    loc: $url,
                    lastmod: $event->updated_at ?? now(),
                    changefreq: 'daily',
                    priority: '0.80',
                );
            });
    }

    private function addUrl(
        array &$urls,
        string $loc,
        mixed $lastmod,
        string $changefreq,
        string $priority,
    ): void {
        $urls[$loc] = [
            'loc' => $loc,
            'lastmod' => $this->formatLastmod($lastmod),
            'changefreq' => $changefreq,
            'priority' => $priority,
        ];
    }

    private function safeRoute(string $routeName, mixed $params = []): ?string
    {
        if (! Route::has($routeName)) {
            return null;
        }

        try {
            return route($routeName, $params);
        } catch (Throwable) {
            return null;
        }
    }

    private function formatLastmod(mixed $value): string
    {
        try {
            return Carbon::parse($value)->toAtomString();
        } catch (Throwable) {
            return now()->toAtomString();
        }
    }

    private function buildXml(array $urls): string
    {
        $writer = new XMLWriter();

        $writer->openMemory();
        $writer->startDocument('1.0', 'UTF-8');

        $writer->startElement('urlset');
        $writer->writeAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');

        foreach ($urls as $url) {
            $writer->startElement('url');

            $writer->writeElement('loc', $url['loc']);
            $writer->writeElement('lastmod', $url['lastmod']);
            $writer->writeElement('changefreq', $url['changefreq']);
            $writer->writeElement('priority', $url['priority']);

            $writer->endElement();
        }

        $writer->endElement();
        $writer->endDocument();

        return $writer->outputMemory();
    }
}