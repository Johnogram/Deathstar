<?php

declare(strict_types=1);

namespace App\Traits;

trait ParseReponse
{
    public function parseResponse($res)
    {
        return json_decode($res);
    }

}
