<?php
/*
 * Fusio - Self-Hosted API Management for Builders.
 * For the current version and information visit <https://www.fusio-project.org/>
 *
 * Copyright (c) Christoph Kappestein <christoph.kappestein@gmail.com>
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

namespace Fusio\Adapter\Laravel\Action;

use Fusio\Engine\ActionAbstract;
use Fusio\Engine\ConfigurableInterface;
use Fusio\Engine\ContextInterface;
use Fusio\Engine\Exception\ConfigurationException;
use Fusio\Engine\Form\BuilderInterface;
use Fusio\Engine\Form\ElementFactoryInterface;
use Fusio\Engine\ParametersInterface;
use Fusio\Engine\RequestInterface;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Http\Kernel as HttpKernelContract;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PSX\Http\Environment\HttpResponseInterface;
use PSX\Http\Exception\InternalServerErrorException;
use PSX\Json\Parser;

/**
 * LaravelInvoke
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://www.fusio-project.org/
 */
class LaravelInvoke extends ActionAbstract implements ConfigurableInterface
{
    public function getName(): string
    {
        return 'Laravel-Invoke';
    }

    public function handle(RequestInterface $request, ParametersInterface $configuration, ContextInterface $context): HttpResponseInterface
    {
        $baseDir = $configuration->get('base_dir');
        if (!is_file($baseDir . '/artisan')) {
            throw new ConfigurationException('Provided an invalid laravel base dir');
        }

        /** @psalm-suppress UnresolvableInclude */
        require $baseDir . '/vendor/autoload.php';

        /** @psalm-suppress UnresolvableInclude */
        $app = require_once $baseDir . '/bootstrap/app.php';

        $arguments = $request->getArguments();
        $path = $this->replaceDynamicValues($configuration->get('path') ?? throw new ConfigurationException('Provided no path'), $arguments);
        $method = $configuration->get('method') ?? throw new ConfigurationException('Provided no method');

        try {
            /** @psalm-suppress UndefinedClass */
            $symfonyRequest = Request::create(
                uri: $path,
                method: $method,
                parameters: $arguments,
                content: Parser::encode($request->getPayload()),
            );

            $kernel = $app->make(HttpKernelContract::class);

            $symfonyResponse = $kernel->handle($symfonyRequest);

            $kernel->terminate($symfonyRequest, $symfonyResponse);

            return $this->response->build($symfonyResponse->getStatusCode(), $symfonyResponse->headers->all(), $symfonyResponse->getContent());
        } catch (\Throwable $e) {
            throw new InternalServerErrorException('Could not invoke controller, got: ' . $e->getMessage(), previous: $e);
        }
    }

    public function configure(BuilderInterface $builder, ElementFactoryInterface $elementFactory): void
    {
        $builder->add($elementFactory->newInput('base_dir', 'Base Directory', 'text', 'The base directory of the laravel app'));
        $builder->add($elementFactory->newInput('path', 'Path', 'text', 'The path passed to the laravel app'));
        $builder->add($elementFactory->newInput('method', 'Method', 'text', 'The method passed to the laravel app'));
    }

    private function replaceDynamicValues(string $path, array $arguments): string
    {
        foreach ($arguments as $name => $value) {
            if (is_string($value)) {
                $path = str_replace(':' . $name, $value, $path);
            }
        }

        return $path;
    }
}
