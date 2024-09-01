<?php

namespace App\Providers;

use App\Services\BookService;
use App\Services\RoleService;
use App\Services\GenreService;
use App\Services\AuthorService;
use App\Services\PermissionService;
use App\Repositories\BookRepository;
use App\Repositories\RoleRepository;
use App\Repositories\GenreRepository;
use App\Repositories\AuthorRepository;
use Illuminate\Support\ServiceProvider;
use App\Repositories\PermissionRepository;
use App\Services\Interfaces\BookServiceInterface;
use App\Services\Interfaces\RoleServiceInterface;
use App\Services\Interfaces\GenreServiceInterface;
use App\Services\Interfaces\AuthorServiceInterface;
use App\Services\Interfaces\PermissionServiceInterface;
use App\Repositories\Interfaces\BookRepositoryInterface;
use App\Repositories\Interfaces\RoleRepositoryInterface;
use App\Repositories\Interfaces\GenreRepositoryInterface;
use App\Repositories\Interfaces\AuthorRepositoryInterface;
use App\Repositories\Interfaces\PermissionRepositoryInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(GenreServiceInterface::class, GenreService::class);
        $this->app->bind(GenreRepositoryInterface::class, GenreRepository::class);

        $this->app->bind(AuthorServiceInterface::class, AuthorService::class);
        $this->app->bind(AuthorRepositoryInterface::class, AuthorRepository::class);

        $this->app->bind(BookServiceInterface::class, BookService::class);
        $this->app->bind(BookRepositoryInterface::class, BookRepository::class);

        $this->app->bind(PermissionServiceInterface::class, PermissionService::class);
        $this->app->bind(PermissionRepositoryInterface::class, PermissionRepository::class);

        $this->app->bind(RoleServiceInterface::class, RoleService::class);
        $this->app->bind(RoleRepositoryInterface::class, RoleRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
