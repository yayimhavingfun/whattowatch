<?php

namespace App\Http\Responses;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class Base implements Responsable
{
    public function __construct(
        protected mixed $data = [],
        public int $statusCode = Response::HTTP_OK,
    ) {}

    /**
     * creates an HTTP response that represents the object
     * 
     * @param Request $request
     * @return Response
     */
    public function toResponse($request)
    {
        return response()->json($this->makeResponseData(), $this->statusCode);
    }

    /**
     * conversion of data to array
     * 
     * @return array
     */
    protected function prepareData(): array
    {
        if ($this->data instanceof Arrayable) {
            return $this->data->toArray();
        }

        return $this->data;
    }

    /**
     * construction of response contents
     * 
     * @return array|null
     */

     abstract protected function makeResponseData(): ?array;
}