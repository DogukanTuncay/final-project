<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use App\Traits\HandlesEvents;

abstract class BaseController extends Controller
{
    use ApiResponseTrait, HandlesEvents;
}
