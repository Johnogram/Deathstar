<?php

declare(strict_types=1);

namespace App\Traits;

trait MakeRequest
{

    /**
     * Make a request to the API
     *
     * @param string $flight_path The flight path we want to fly
     *
     * @return array The response code, message and map provided from the API
     */
    protected function makeRequest(string $flight_path) : array
    {
        $res = $this->client->request('GET', 'http://deathstar.victoriaplum.com/alliance.php', [
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
