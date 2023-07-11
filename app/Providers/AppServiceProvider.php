<?php

namespace App\Providers;

use Illuminate\Support\Str;
use Illuminate\Support\Stringable;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Stringable::macro('initials', function(){
            $words = preg_split("/\s+/", $this);
            $initials = "";

            foreach ($words as $w) {
              $initials .= $w[0];
            }

            return new static($initials);
        });

        Str::macro('initials', function(string $string){
            return (string) (new Stringable($string))->initials();
        });
    }
}
