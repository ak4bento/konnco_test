<?php

namespace App\Http\Searches;

use App\Http\Searches\Filters\Payment\Search;
use App\Http\Searches\Filters\Payment\Sort;
use App\Models\Transaction;

class PaymentSearch extends HttpSearch
{
    protected function passable()
    {
        return Transaction::where('user_id', auth()->user()->id);
    }

    protected function filters(): array
    {
        return [
            Sort::class,
            Search::class,
        ];
    }

    protected function thenReturn($paymentSearch)
    {
        return $paymentSearch;
    }
}
