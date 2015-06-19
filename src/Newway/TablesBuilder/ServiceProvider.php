<?php namespace Newway\TablesBuilder;

use Illuminate\Support\ServiceProvider as SP;

class ServiceProvider extends SP
{
    public function register()
    {

    }
    public function boot()
    {
        $this->loadTranslationsFrom(__DIR__.'/../../lang', 'tables_builder');
    }
}