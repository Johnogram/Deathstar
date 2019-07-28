<?php

declare(strict_types=1);

namespace App\Traits;


use App\Traits\ParseReponse;

trait MakeRequest
{

    use ParseReponse;

    public function makeRequest(string $flight_path)
    {
        $res = $this->_client->request('GET', 'http://deathstar.victoriaplum.com/alliance.php', [
            'query' => [
                'name' => "JohnOGram",
                'path' => $flight_path,
            ],
        ]);
        $res_body = $this->parseResponse($res->getBody()->getContents());

        return [
            'status' => $res->getStatusCode(),
            'message' => $res_body->message,
            'map' => $res_body->map,
        ];
    }
}
