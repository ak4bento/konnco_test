<?php

namespace App\Http\Searches\Filters\Payment;

use App\Http\Searches\Contracts\FilterContract;
use App\Models\Transaction;
use Closure;
use Illuminate\Database\Eloquent\Builder;

class Sort implements FilterContract
{
    /** @var string */
    protected $defaultSortField = 'id';

    /** Editable */
    public function classes(): array
    {
        return [
            new Transaction(),
        ];
    }

    /**
     * @return mixed
     */
    public function handle(Builder $query, Closure $next)
    {
        $sortField = request('sort_field', $this->defaultSortField);
        $sortOrder = request('sort_order', 'asc');

        $isSortAvailable = $this->isSortFieldAvailable($sortField);

        if ($isSortAvailable) {
            $query->orderBy($sortField, $sortOrder);
        }

        return $next($query);
    }

    protected function isSortFieldAvailable(string $sort): bool
    {
        $fillable = $this->getAllFillable();

        return in_array($sort, $fillable);
    }

    protected function getAllFillable(): array
    {
        $classes = $this->classes();

        $fillable = [];

        foreach ($classes as $class) {
            $keys = $class->getTable();

            foreach ($class->getFillable() as $fill) {
                $fillable[] = $keys.'.'.$fill;
            }
        }

        return $fillable;
    }
}
