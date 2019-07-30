<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Droids\Droid;

class NavController extends Controller
{
    
    public function start()
    {
        $droid = new Droid();

        [$path, $layout] = $droid->letsFly();

        return view('complete', [
            'path' => $path,
            'layout' => $layout,
        ]); 
    }

}
