<?php

namespace App\Http\Searches;

use App\Http\Searches\Resources\Form;
use App\Http\Searches\Traits\BaseFoundation;
use Illuminate\Contracts\Container\Container as Laravel;
use Illuminate\Http\Request;
use Illuminate\Pipeline\Pipeline;
use RuntimeException;

abstract class HttpSearch extends Form
{
    use BaseFoundation;

    /** @var array */
    protected $parameters = [];

    /** @var Pipeline */
    protected $pipeline;

    /** @var Laravel|null */
    protected $laravel;

    /** @var Request */
    protected $request;

    /** @var array */
    protected $filters = [];

    /**
     * Undocumented function
     *
     * @return void
     */
    public function __construct(Pipeline $pipeline, Request $request, ?Laravel $laravel = null)
    {
        $this->pipeline = $pipeline;
        $this->request = $request;
        $this->laravel = $laravel;
    }

    /**
     * Search using pipeline.
     *
     * @param  mixed  ...$params
     * @return mixed
     */
    public function apply(...$params)
    {
        if (! empty($params)) {
            $this->parameters = $params['0'];
        } elseif (! empty($this->request)) {
            $this->parameters = $this->request->all();
        }

        if (! method_exists($this, 'passable')) {
            throw new RuntimeException('passable method not exists.');
        }

        $result = $this->pipeline->send($this->{'passable'}(...$params))
            ->through($this->parsedFilters())
            ->thenReturn();

        return $this->thenReturn($result) ?? $result;
    }

    protected function filters(): array
    {
        return $this->filters;
    }

    /**
     * @param  mixed  $result
     * @return mixed
     */
    protected function thenReturn($result)
    {
        return $result;
    }

    protected function parsedFilters(): array
    {
        $filters = collect($this->filters())->map(function ($filter) {
            if (is_object($filter) || ! class_exists($filter)) {
                return $filter;
            }

            return $this->resolveClassIdentification($filter);
        });

        return $filters->all();
    }
}
