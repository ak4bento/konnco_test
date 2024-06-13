<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Constants extends Model
{
    const RESOURCE_OK_STATUS = 200;

    const RESOURCE_CREATED_STATUS = 201;

    const RESOURCE_BAD_REQUEST_STATUS = 400;

    const RESOURCE_UNAUTHORIZED_STATUS = 401;

    const RESOURCE_NOT_FOUND_STATUS = 404;

    const RESOURCE_INTERNAL_ERROR_STATUS = 500;
}
