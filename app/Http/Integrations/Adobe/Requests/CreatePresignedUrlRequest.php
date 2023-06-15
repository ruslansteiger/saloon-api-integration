<?php

namespace App\Http\Integrations\Adobe\Requests;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

class CreatePresignedUrlRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function resolveEndpoint(): string
    {
        return '/assets';
    }

    public function defaultBody()
    {
        return [
            'mediaType' => 'application/pdf',
        ];
    }
}
