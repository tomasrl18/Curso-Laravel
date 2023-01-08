<?php

namespace App\Providers;

use App\Http\ViewComposers\UserFieldsComposer;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Blade::directive('render', function ($expression) {
            $parts = explode(',', $expression, 2);

            $component = $parts[0];
            $args = trim($parts[1] ?? '[]');

            return "<?php echo app('App\Http\ViewComponents\\\\' . {$component}, {$args})->toHtml() ?>";
        });
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
