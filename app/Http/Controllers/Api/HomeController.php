<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function home()
    {
        return $this->response()->success('access success');
    }
}
