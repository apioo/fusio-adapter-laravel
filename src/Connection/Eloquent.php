<?php
/*
 * Fusio is an open source API management platform which helps to create innovative API solutions.
 * For the current version and information visit <https://www.fusio-project.org/>
 *
 * Copyright 2015-2023 Christoph Kappestein <christoph.kappestein@gmail.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Fusio\Adapter\Laravel\Connection;

use Fusio\Engine\ConnectionInterface;
use Fusio\Engine\Form\BuilderInterface;
use Fusio\Engine\Form\ElementFactoryInterface;
use Fusio\Engine\ParametersInterface;
use Illuminate\Container\Container;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Events\Dispatcher;

/**
 * Eloquent
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.gnu.org/licenses/agpl-3.0
 * @link    https://www.fusio-project.org/
 */
class Eloquent implements ConnectionInterface
{
    public function getName(): string
    {
        return 'Eloquent';
    }

    public function getConnection(ParametersInterface $config): Capsule
    {
        $capsule = new Capsule();

        $capsule->addConnection([
            'driver'    => $config->get('driver') ?: 'mysql',
            'host'      => $config->get('host'),
            'database'  => $config->get('database'),
            'username'  => $config->get('username'),
            'password'  => $config->get('password'),
            'prefix'    => $config->get('prefix') ?: '',
        ]);

        $capsule->setEventDispatcher(new Dispatcher(new Container()));
        $capsule->setAsGlobal(); // usually we dont want this but for UX we leave this on
        $capsule->bootEloquent();

        return $capsule;
    }

    public function configure(BuilderInterface $builder, ElementFactoryInterface $elementFactory): void
    {
        $builder->add($elementFactory->newSelect('driver', 'Driver', ['mysql' => 'MYSQL', 'pgsql' => 'Postgres', 'sqlsrv' => 'MSSQL'], 'Eloquent driver'));
        $builder->add($elementFactory->newInput('host', 'Host', 'text', 'The database host'));
        $builder->add($elementFactory->newInput('database', 'Database', 'text', 'The database name'));
        $builder->add($elementFactory->newInput('username', 'Username', 'text', 'The database username'));
        $builder->add($elementFactory->newInput('password', 'Password', 'text', 'The database password'));
        $builder->add($elementFactory->newInput('prefix', 'Prefix', 'text', 'Table prefix of the database'));
    }
}
