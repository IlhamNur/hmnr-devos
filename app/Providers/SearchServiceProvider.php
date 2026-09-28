<?php

namespace App\Providers;

use App\Services\Search\KeywordSearchService;
use App\Services\Search\SearchInterface;
use Illuminate\Support\ServiceProvider;

class SearchServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(SearchInterface::class, KeywordSearchService::class);
    }
}

