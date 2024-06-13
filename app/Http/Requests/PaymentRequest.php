<?php

namespace App\Http\Requests;

class PaymentRequest extends FailFormRequest
{
    public function __construct()
    {
        $rules = [
            'amount' => 'required|numeric',
        ];
        parent::__construct($rules);
    }
}
