<?php

namespace App\Http\Requests;

class PaymentStatusRequest extends FailFormRequest
{
    public function __construct()
    {
        $rules = [
            'amount' => 'required|numeric',
        ];
        parent::__construct($rules);
    }
}
