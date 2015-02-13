<?php namespace Newway\TablesBuilder;

use Illuminate\Support\ServiceProvider as SP;

class ServiceProvider extends SP
{
    public function register()
    {

    }
    public function boot()
    {
        $this->package('newway/tables_builder');
    }
}