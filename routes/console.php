<?php

use App\Http\Integrations\Adobe\AdobeConnector;
use App\Http\Integrations\Adobe\Requests\CheckStatusRequest;
use App\Http\Integrations\Adobe\Requests\CreatePresignedUrlRequest;
use App\Http\Integrations\Adobe\Requests\ExportToWordRequest;
use App\Http\Integrations\Adobe\Requests\UploadDocumentRequest;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

/*
 * Basic Overview of all the requests we are doing:
 * 1) Create Presigned URL
 * 2) Upload the document using the presigned URL
 * 3) Export to Word
 * 4) Check the Status of the Export
 * 5) Download the document
 */
Artisan::command('convert', function (AdobeConnector $connector) {
    // 1) Create presigned URL
    $response = $connector->send(new CreatePresignedUrlRequest());

    // Example: 'https://dcplatformstorageservice-prod-eu-west-1.s3-accelerate.amazonaws.com/b7a4611d985048c7ac4a6c30d3c1844b_6AD32A806408838B0A495E11%40techacct.adobe.com/5d19bada-c5d7-4dad-a96b-12633d802f2b?X-Amz-Security-Token=FwoGZXIvYXdzELT%2F%2F%2F%2F%2F%2F%2F%2F%2F%2FwEaDBn4HNpk%2BdKw%2BwfyCSLUAdwp6WkdATqtyHvzrpF9YsmRg4Odb0r%2F6jE8UXjEC0DvP85x6O6PB8nM8GQ%2B%2FOBR3w9Oe3%2FqDiNXjgSxXB%2FRtTW5jkzl9nPlNZxUbXXgde4SFYbSshLJDtJdojF2phmlquhIA7DWBePKWbENciKpqYDazTPKZ1x8GjBfJ20XtQNjP1MtQkWEBKBONWVCXGNitIbsdXc2%2BFM7GnxTrnW93BOrA2OGsrRtV0fEuwXjKttropj9mKRr%2BOVtDjlqZkuQwV9H5unrtfZbEsSGep9ykRvZQomlKInepqAGMi0wMZsywKh4JBQad2%2FpDYPjwD3JSa7peZ%2BpUHheRq9kiIWtX%2F%2Bid9OdnOwH5hQ%3D&X-Amz-Algorithm=AWS4-HMAC-SHA256&X-Amz-Date=20230309T101743Z&X-Amz-SignedHeaders=content-type%3Bhost&X-Amz-Expires=3600&X-Amz-Credential=ASIAWD2N7EVPEIBGIO7G%2F20230309%2Feu-west-1%2Fs3%2Faws4_request&X-Amz-Signature=289a4e08f7f9222d0e4a9d4886fd8a028244792c8f84964f2e6d5000a1ca14d8';
    $uploadUri = $response->json('uploadUri');
    // Example: 'urn:aaid:AS:EW1:028755ba-1969-4f7e-a677-9ab83430b72b';
    $assetId = $response->json('assetID');

    // 2) Upload the document using the presigned URL
    (new UploadDocumentRequest($uploadUri, Storage::get('convince.pdf')))->send();

    // 3) Export to Word
    $locationOriginal = $connector->send(new ExportToWordRequest($assetId))
        ->header('location');

    // Example: '/operation/exportpdf/QqEKaI6qBbWCsu3ul8jKbdkGSxT44duE/status'
    $location = str($locationOriginal)
        ->after('/operation/exportpdf/')
        ->before('/status')
        ->toString();

    // 4) Check the Status of the Export
    $response = $connector->sendAndRetry(new CheckStatusRequest($location), 10, 2_000);

    // Example: 'https://dcplatformstorageservice-prod-eu-west-1.s3-accelerate.amazonaws.com/b7a4611d985048c7ac4a6c30d3c1844b_6AD32A806408838B0A495E11%40techacct.adobe.com/ca3d4dac-8623-41c0-926a-aadff82f789c?X-Amz-Security-Token=FwoGZXIvYXdzEHMaDOLP%2BT1VtZ3s55b3xCLUAWkAcVDNS8zrkiOA11Ed2x2D5phqFGWqTYdvhiuoheYnA3hddwg%2FwnoAZgwf%2BUQpQ2nLbRmYrF8f5dxfzkDSfiMj5WwSkb2jHmHX%2B%2FGf4R8SK%2Fzq7YGP%2BuroM2k5PH%2BOOEP5cj%2FMSJBdfdSJKH89hLNxQbYushUcD2VzIl1KvYClDxnbsah7PrXeQxjmdBXuYQI1e32QKiWVe5Z%2BiJhKZTgfUYoSM4Sm6KlsRPIgvY4WBpNG8ujYrpasbSY0AhnMd4WWXcEAQ6Sn8xgqxc8v2NnyUeZ7KLva0KAGMi29V%2FQfLcl2eWreXYOeBP7i8mIQBFVp75DUnbdIIaoDqXvKSdZqQQXnc7Jqy8A%3D&X-Amz-Algorithm=AWS4-HMAC-SHA256&X-Amz-Date=20230317T090812Z&X-Amz-SignedHeaders=host&X-Amz-Expires=3600&X-Amz-Credential=ASIAWD2N7EVPPJVRS7MR%2F20230317%2Feu-west-1%2Fs3%2Faws4_request&X-Amz-Signature=c3b7e10537bc0e96d344344a0ca1c35ddd5773397871498dcb74c19e199797f7'
    $downloadUrl = $response->json('asset.downloadUri');

    // 5) Download the document
    $content = Http::get($downloadUrl)->body();
    Storage::put('convince.docx', $content);

    // Show success message
    $this->comment(PHP_EOL.PHP_EOL.PHP_EOL.'    🎊🎊🎊🎊🎊🎊🎊🎊🎊🎊🎊🎊🎊🎊🎊🎊🎊🎊🎊🎊');
    $this->comment('    🎊 Conversion completed successfully! 🎊');
    $this->comment('    🎊🎊🎊🎊🎊🎊🎊🎊🎊🎊🎊🎊🎊🎊🎊🎊🎊🎊🎊🎊'.PHP_EOL.PHP_EOL);
});
