<?php

namespace App\Http\Requests;

use App\Models\Constants;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class FailFormRequest extends FormRequest
{
    protected $stopOnFirstFailure = true;

    private $rules = [];

    private $authorize = true;

    public function __construct($rules, $authorize = true)
    {
        $this->rules = $rules;
        $this->authorize = $authorize;
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->authorize;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return $this->rules;
    }

    protected function failedValidation(Validator $validator)
    {
        $response = new JsonResponse([
            'message' => $validator->errors()->first(),
        ], Constants::RESOURCE_BAD_REQUEST_STATUS);

        throw new ValidationException($validator, $response);
    }
}
