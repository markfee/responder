<?php namespace Markfee\Responder;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\AliasLoader;

class ResponderServiceProvider extends ServiceProvider {

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        AliasLoader::getInstance()->alias('Responder\ResponderTrait',       'Markfee\Responder\Responder\ResponderTrait');
        AliasLoader::getInstance()->alias('Responder\TransformTrait',       'Markfee\Responder\Responder\TransformTrait');
        AliasLoader::getInstance()->alias('Responder\PaginatorTrait',       'Markfee\Responder\Responder\PaginatorTrait');
        AliasLoader::getInstance()->alias('Responder\ResponderInterface',   '\Markfee\Responder\Responder\ResponderInterface');
        AliasLoader::getInstance()->alias('Responder\Transformer',          '\Markfee\Responder\Responder\Transformer');
        AliasLoader::getInstance()->alias('Responder\Response',             '\Markfee\Responder\Responder\Response');
        AliasLoader::getInstance()->alias('Responder\ApiResponse',          '\Markfee\Responder\Responder\ApiResponse');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array('responder');
    }

}
