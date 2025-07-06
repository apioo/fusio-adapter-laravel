<?php

use Fusio\Adapter\Laravel\Action\LaravelInvoke;
use Fusio\Adapter\Laravel\Connection\Eloquent;
use Fusio\Engine\Adapter\ServiceBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $container) {
    $services = ServiceBuilder::build($container);
    $services->set(Eloquent::class);
    $services->set(LaravelInvoke::class);
};
