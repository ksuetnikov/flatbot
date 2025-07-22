<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\Meter;

use App\DTO\SubscribeUser\SubscribeUserDTO;
use App\Http\Controllers\Controller;
use App\Http\Resources\SubscribeUser\SubscribeUserGetResource;
use App\Models\Object\ObjectModel;
use App\Models\Object\ObjectProperty;
use App\Services\SubscribeUser\SubscribeUserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * @author Kirill Suetnikov
 */
class MeterController extends Controller
{
   public function create(Request $request)
   {
       \Log::info('da!');
       \Log::info($request);
       $property = $request['value'];
       $id = $request['id'];
       $objectProperty = ObjectProperty::findOrFail($id);
       $objectProperty->value = $property;
       $objectProperty->save();
   }
}
