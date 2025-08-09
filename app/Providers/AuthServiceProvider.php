<?php

namespace App\Providers;

use App\Models\Kegiatan;
use App\Models\User;
use App\Policies\KegiatanPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Kegiatan::class => KegiatanPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Berikan superadmin semua izin sebelum memanggil kebijakan lainnya
        Gate::before(function (User $user, string $ability) {
            if ($user->role === 'superadmin') {
                return true;
            }
        });
    }
}

