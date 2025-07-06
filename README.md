Fusio-Adapter-Laravel
=====

[Fusio] adapter which helps to integrate features of Laravel. You can install
the adapter with the following steps inside your Fusio project:

    composer require fusio/adapter-laravel
    php bin/fusio system:register "Fusio\Adapter\Laravel\Adapter"

[Fusio]: https://www.fusio-project.org/

## Action

Through the Laravel-Invoke action, you can directly invoke specific controller logic
of your Laravel app through Fusio. This works only in case Fusio is on the same server
as your Laravel app since the action includes and executes the app. This helps to
quickly expose the logic of your Laravel app as clean API through Fusio.

Note: This action is currently in beta phase, please let us know in case you have
specific requirements regarding an integration.

## Connection

Through the Eloquent connection you can build API endpoints using the Eloquent
ORM i.e.:

```php
<?php

use \Illuminate\Database\Capsule\Manager as Capsule;

$connection = $connector->getConnection('eloquent');

$actions = Capsule::table('fusio_action')->where('id', '>', 2)->get();

return $response->build(200, [], [
    'actions' => $actions,
]);
```
