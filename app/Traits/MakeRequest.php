<?php

declare(strict_types=1);

namespace App\Traits;

trait MakeRequest
{

    protected function makeRequest(string $flight_path) : array
    {
        $res = $this->_client->request('GET', 'http://deathstar.victoriaplum.com/alliance.php', [
            'query' => [
                'name' => "JohnOGram",
                'path' => $flight_path,
            ],
        ]);

        $res_body = json_decode($res->getBody()->getContents(), true);
        $map = explode("\n", $res_body['map']);

        return [
            'status' => $res->getStatusCode(),
            'message' => $res_body['message'],
            'map' => array_reverse($map),
        ];
    }
}
