Fusio-Adapter-Laravel
=====

[Fusio] adapter which helps to integrate features of Laravel. You can install
the adapter with the following steps inside your Fusio project:

    composer require fusio/adapter-laravel
    php bin/fusio system:register "Fusio\Adapter\Laravel\Adapter"

[Fusio]: https://www.fusio-project.org/

## Example

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


