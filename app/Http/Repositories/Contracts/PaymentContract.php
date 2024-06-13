<?php

namespace App\Http\Repositories\Contracts;

interface PaymentContract
{
    public function createTransaction(array $request);

    public function updateTransaction(array $request, $id);

    public function getUserTransactionSummary();

    public function getAllTransactionSummary();
}
