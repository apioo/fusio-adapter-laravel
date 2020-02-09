<?php
/*
 * Fusio
 * A web-application to create dynamically RESTful APIs
 *
 * Copyright (C) 2015-2020 Christoph Kappestein <christoph.kappestein@gmail.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
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
 * @link    http://fusio-project.org
 */
class Eloquent implements ConnectionInterface
{
    public function getName()
    {
        return 'Eloquent';
    }

    /**
     * @param \Fusio\Engine\ParametersInterface $config
     * @return \Illuminate\Database\Capsule\Manager
     */
    public function getConnection(ParametersInterface $config)
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

    public function configure(BuilderInterface $builder, ElementFactoryInterface $elementFactory)
    {
        $builder->add($elementFactory->newSelect('driver', 'Driver', ['mysql' => 'MYSQL', 'pgsql' => 'Postgres', 'sqlsrv' => 'MSSQL'], 'Eloquent driver'));
        $builder->add($elementFactory->newInput('host', 'Host', 'text', 'The database host'));
        $builder->add($elementFactory->newInput('database', 'Database', 'text', 'The database name'));
        $builder->add($elementFactory->newInput('username', 'Username', 'text', 'The database username'));
        $builder->add($elementFactory->newInput('password', 'Password', 'text', 'The database password'));
        $builder->add($elementFactory->newInput('prefix', 'Prefix', 'text', 'Table prefix of the database'));
    }
}
