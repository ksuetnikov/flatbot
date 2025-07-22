<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\Meter;

use App\Http\Controllers\Controller;
use App\Models\Object\ObjectProperty;
use Illuminate\Http\Request;

/**
 * @author Kirill Suetnikov
 */
class MeterController extends Controller
{
    /**
     * @param Request $request
     *
     * @return void
     */
    public function create(Request $request): void
    {
        $property = $request['value'];
        $id = $request['id'];
        $objectProperty = ObjectProperty::findOrFail($id);
        $objectProperty->value = $property;
        $objectProperty->save();
    }
}
