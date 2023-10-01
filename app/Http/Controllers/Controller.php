<?php

namespace App\Http\Controllers;

use App\Http\Responses\PaginatedResponse;
use App\Http\Responses\Success;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function success($data, ?int $code = Response::HTTP_OK)
    {
        return new Success($data, $code);
    }

    protected function paginate($data, ?int $code = Response::HTTP_OK)
    {
        return new PaginatedResponse($data, $code);
    }
}
