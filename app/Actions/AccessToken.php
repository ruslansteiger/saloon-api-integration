<?php

namespace App\Actions;

use Firebase\JWT\JWT;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class AccessToken
{
    public static function get(): string
    {
        return Cache::remember(
            'adobe-access-token',
            now()->addDay(),
            fn () => Http::asMultipart()
                ->post('https://ims-na1.adobelogin.com/ims/exchange/jwt', [
                    'client_id' => config('services.adobe.client_id'),
                    'client_secret' => config('services.adobe.client_secret'),
                    'jwt_token' => self::generateJwtToken(),
                ])
                ->throw()
                ->json('access_token'),
        );
    }

    protected static function generateJwtToken(): string
    {
        $payload = [
            'exp' => Carbon::now()->addDay()->unix(),
            'iss' => '6CD626776408834C0A495E50@AdobeOrg',
            'sub' => '6AD32A806408838B0A495E11@techacct.adobe.com',
            'https://ims-na1.adobelogin.com/s/ent_documentcloud_sdk' => true,
            'aud' => 'https://ims-na1.adobelogin.com/c/b7a4611d985048c7ac4a6c30d3c1844b',
        ];

        $key = config('services.adobe.private_key');

        return JWT::encode($payload, $key, 'RS256');
    }
}
