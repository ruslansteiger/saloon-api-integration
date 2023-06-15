<?php

namespace App\Http\Integrations\Adobe\Requests;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

class ExportToWordRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(
        public string $assetId,
    ) {
    }

    public function resolveEndpoint(): string
    {
        return '/operation/exportpdf';
    }

    public function defaultBody()
    {
        return [
            'assetID' => $this->assetId,
            'targetFormat' => 'docx',
            'ocrLang' => 'en-US',
        ];
    }
}
