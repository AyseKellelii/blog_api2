<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Document;
use App\Models\Post;
use App\Models\Tag;
use App\Policies\CategoryPolicy;
use App\Policies\DocumentPolicy;
use App\Policies\PostPolicy;
use App\Policies\TagPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [

        Category::class => CategoryPolicy::class,
        Tag::class => TagPolicy::class,
        Post::class => PostPolicy::class,
        Document::class => DocumentPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
