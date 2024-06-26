<?php

namespace App\Http\Searches\Filters\Payment;

use App\Http\Searches\Contracts\FilterContract;
use Closure;
use Illuminate\Database\Eloquent\Builder;

class Search implements FilterContract
{
    /** @var string|null */
    protected $search;

    /**
     * @param  string|null  $search
     * @return void
     */
    public function __construct($search)
    {
        $this->search = $search;
    }

    /**
     * @return mixed
     */
    public function handle(Builder $query, Closure $next)
    {
        if (! $this->keyword()) {
            return $next($query);
        }

        $query->where(function ($q) {
            $q->where('status', 'LIKE', '%'.$this->search.'%');
            $q->orWhere('amount', 'LIKE', '%'.$this->search.'%');
        });

        return $next($query);
    }

    /**
     * Get search keyword.
     *
     * @return mixed
     */
    protected function keyword()
    {
        if ($this->search) {
            return $this->search;
        }

        $this->search = request('search', null);

        return request('search');
    }
}
