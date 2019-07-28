<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Droids\Droid;
use App\Boards\BoardLayout;

class NavController extends Controller
{
    
    public function start()
    {
        $droid = new Droid();
        $layout = new BoardLayout();

        $initial_path = "ffffffffff";

        $journey = $droid->startFlight($initial_path);

        print_r($journey);

        return view('navigation');
    }

}
