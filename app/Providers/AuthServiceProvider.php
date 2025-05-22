<?php

namespace App\Providers;

use App\Models\Producto;
use App\Models\TipoServicio;
use App\Models\Servicio;
use App\Policies\ProductoPolicy;
use App\Policies\TipoServicioPolicy;
use App\Policies\ServicioPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        TipoServicio::class => TipoServicioPolicy::class,
        Servicio::class => ServicioPolicy::class,
        Producto::class => ProductoPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}
