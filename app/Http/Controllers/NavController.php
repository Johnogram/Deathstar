<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Droids\Droid;
use \Illuminate\View\View;

class NavController extends Controller
{
    
    /**
     * Start the flight
     *
     * @return View The view to show
     */
    public function start() : View
    {
        $droid = new Droid();

        [$path, $layout] = $droid->letsFly();

        return view('complete', [
            'path' => $path,
            'layout' => array_reverse($layout),
        ]);
    }
}
