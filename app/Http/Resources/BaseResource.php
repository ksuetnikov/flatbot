<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @author Kirill Suetnikov
 */
class BaseResource extends JsonResource
{
    protected int $statusCode = 200;

}
