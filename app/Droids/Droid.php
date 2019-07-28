<?php

declare(strict_types=1);

namespace App\Droids;

use App\Traits\ParseReponse;
use GuzzleHttp\Client;

class Droid
{

    use ParseReponse;
    
    private $_client;

    private $_nickname;

    private $_forward;
    private $_left;
    private $_right;

    public function __construct()
    {
        $this->_client = new Client([
            'http_errors' => false,
        ]);

        $this->_nickname = "JohnOGram";
        $this->_forward = 'f';
        $this->_left = 'l';
        $this->_right = 'r';
    }

    public function startFlight(string $flight_path) : array
    {
        $res = $this->_client->request('GET', 'http://deathstar.victoriaplum.com/alliance.php', [
            'query' => [
                'name' => $this->_nickname,
                'path' => $flight_path,
            ],
        ]);

        $res_body = $this->parseResponse($res->getBody()->getContents());

        return [
            'status' => $res->getStatusCode(),
            'map' => $res_body->map,
        ];
    }

}
