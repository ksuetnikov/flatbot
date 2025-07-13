<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\Objects;

use App\Http\Controllers\Controller;
use App\Models\Object\ObjectModel;
use Illuminate\Support\Facades\Auth;

/**
 * @author Kirill Suetnikov
 */
class ObjectController extends Controller
{
public function index()
{
    $user = Auth::user();
    $objects = ObjectModel::where('user_id', $user->id)->get();

    return response()->json($objects);
}
}
