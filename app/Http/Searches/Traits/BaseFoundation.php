<?php

namespace App\Http\Searches\Traits;

use Illuminate\Contracts\Container\Container as Laravel;
use Illuminate\Support\Str;
use RuntimeException;

trait BaseFoundation
{
    /** @var Laravel|null */
    protected $laravel;

    /**
     * @param  \ReflectionParameter[]  $params
     */
    protected function mapParameters(array $params): array
    {
        $mappedParams = [];

        foreach ($params as $param) {
            $key = $param->name;

            $input = $this->get($key) ?? $this->get(Str::snake($key));

            if ($input || $param->allowsNull()) {
                $mappedParams[$param->name] = $input;
            }
        }

        return $mappedParams;
    }

    /**
     * change class to string to Builder Object
     *
     * @param  class-string  $currentClass
     * @return mixed
     */
    protected function resolveClassIdentification($currentClass)
    {
        $constructor = (new \ReflectionClass($currentClass))->getConstructor();

        if (! $constructor) {
            return $currentClass;
        }

        $parameters = $this->mapParameters($constructor->getParameters());

        return $this->getLaravel()->make($currentClass, $parameters);
    }

    /**
     * Get the Laravel container instance.
     *
     * @return Laravel
     *
     * @throws RuntimeException
     */
    protected function getLaravel()
    {
        if (! $this->laravel) {
            throw new RuntimeException('Laravel container instance has not been passed to the Pipeline.');
        }

        return $this->laravel;
    }
}
