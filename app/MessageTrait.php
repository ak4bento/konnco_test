<?php

namespace App;

use App\Models\Constants;

trait MessageTrait
{
    protected function Ok(): array
    {
        return [
            'status' => Constants::RESOURCE_OK_STATUS,
            'message' => 'Success',
            'description' => 'The request was successful and the server responded with the requested data.',
        ];
    }

    protected function Created(): array
    {
        return [
            'status' => Constants::RESOURCE_CREATED_STATUS,
            'message' => 'Resource Created',
            'description' => 'The request was successful and a new resource was created as a result. The URI of the newly created resource is available in the response.',
        ];
    }

    protected function BadRequest(): array
    {
        return [
            'status' => Constants::RESOURCE_BAD_REQUEST_STATUS,
            'message' => 'Bad Request',
            'description' => 'The server could not understand the request due to invalid syntax. Please check your request and try again.',
        ];
    }

    protected function Unauthorized(): array
    {
        return [
            'status' => Constants::RESOURCE_UNAUTHORIZED_STATUS,
            'message' => 'Unauthorized',
            'description' => 'You must be authenticated to access this resource. Please provide valid authentication credentials and try again.',
        ];
    }

    protected function NotFound(): array
    {
        return [
            'status' => Constants::RESOURCE_NOT_FOUND_STATUS,
            'message' => 'Not Found',
            'description' => 'The server could not find the requested resource. Please check the URL and try again.',
        ];
    }

    protected function Error(): array
    {
        return [
            'status' => Constants::RESOURCE_INTERNAL_ERROR_STATUS,
            'message' => 'Internal Server Error',
            'description' => 'The server encountered an internal error and was unable to complete your request. Please try again later or contact support if the issue persists.',
        ];
    }
}
