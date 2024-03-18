<?php

namespace App\Traits;

use Illuminate\Support\Facades\Http;

trait WhatsAppApiTrait
{
    protected $base_url = 'https://api.botmastersender.com/api/v2/';
    protected $senderId = '919727793123';
    protected $authToken = '5cd77122-acbe-419d-a662-1300d3b20565';
// mediaurl
// receiverId
// messageText
    protected function getUserActivityLogJSON($main_request)
    {
        $default_request = [
            [
                'name' => 'senderId',
                'contents' => $this->senderId
            ],
            [
                'name' => 'authToken',
                'contents' => $this->authToken
            ]
        ];
        // Merge default request with main request
        $options['multipart'] = array_merge($default_request, $main_request);

        $response = Http::post($this->base_url . '?action=send', $options);

        return $response->json();
    }
}
