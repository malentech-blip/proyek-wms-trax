<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate; // Pastikan ini tidak di-comment
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\User; // <-- TAMBAHKAN BARIS INI
use Illuminate\Support\Facades\Gate; // <-- TAMBAHKAN BARIS INI

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {

        
        // --- TAMBAHKAN KODE INI ---
        // Kode ini akan berjalan sebelum semua pengecekan izin lainnya.
        // Jika user memiliki peran 'admin', maka secara otomatis akan diberikan akses.
        Gate::before(function (User $user, string $ability) {
            
            return $user->hasRole('admin') ? true : null;
        });
        // -------------------------
    }
}