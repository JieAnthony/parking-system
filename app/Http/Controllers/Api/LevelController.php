<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LicenseRequest;
use App\Models\Level;
use App\Services\LevelService;

class LevelController extends Controller
{
    /**
     * @var LevelService
     */
    public $levelService;

    public function __construct(LevelService $levelService)
    {
        $this->levelService = $levelService;
    }

    public function index()
    {
    }

    public function buy(LicenseRequest $request, Level $level)
    {
    }
}
