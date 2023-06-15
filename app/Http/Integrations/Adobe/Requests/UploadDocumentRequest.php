<?php

namespace App\Http\Integrations\Adobe\Requests;

use Saloon\Contracts\Body\HasBody;
use Saloon\Data\MultipartValue;
use Saloon\Enums\Method;
use Saloon\Http\SoloRequest;
use Saloon\Traits\Body\HasMultipartBody;
use Saloon\Traits\Plugins\AlwaysThrowOnErrors;

class UploadDocumentRequest extends SoloRequest implements HasBody
{
    use HasMultipartBody;
    use AlwaysThrowOnErrors;

    protected Method $method = Method::PUT;

    public function __construct(
        public string $endpoint,
        public string $content,
    ) {
    }

    public function resolveEndpoint(): string
    {
        return $this->endpoint;
    }

    protected function defaultHeaders(): array
    {
        return [
            'Content-Type' => 'application/pdf',
        ];
    }

    public function defaultBody()
    {
        return [
            new MultipartValue('document', $this->content),
        ];
    }
}
