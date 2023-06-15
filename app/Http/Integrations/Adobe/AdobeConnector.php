<?php

namespace App\Http\Integrations\Adobe;

use App\Actions\AccessToken;
use Saloon\Http\Connector;
use Saloon\Traits\Plugins\AcceptsJson;
use Saloon\Traits\Plugins\AlwaysThrowOnErrors;

class AdobeConnector extends Connector
{
    use AcceptsJson;
    use AlwaysThrowOnErrors;

    public function resolveBaseUrl(): string
    {
        return 'https://pdf-services-ew1.adobe.io';
    }

    protected function defaultHeaders(): array
    {
        return [
            'Authorization' => 'Bearer '.AccessToken::get(),
            'X-Api-Key' => config('services.adobe.client_id'),
        ];
    }
}
