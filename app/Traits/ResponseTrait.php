<?php

namespace App\Traits;

use App\Http\Response;

trait ResponseTrait
{
    /**
     * @return Response
     */
    protected function response()
    {
        return app(Response::class);
    }
}
