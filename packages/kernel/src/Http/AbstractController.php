<?php

namespace Shelter\Kernel\Http;

use Illuminate\Routing\Controller;

abstract class AbstractController extends Controller
{
    use ControllerUsesTrait;
}
