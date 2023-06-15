<?php

namespace App\Http\Integrations\Adobe\Requests;

use Saloon\Contracts\Response;
use Saloon\Enums\Method;
use Saloon\Http\Request;

class CheckStatusRequest extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        public string $location,
    ) {
    }

    public function resolveEndpoint(): string
    {
        return "/operation/exportpdf/{$this->location}/status";
    }

    public function hasRequestFailed(Response $response): ?bool
    {
        return $response->json('status') !== 'done';
    }
}
