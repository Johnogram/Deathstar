<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Droids\Droid;
use App\Boards\BoardLayout;

class NavController extends Controller
{
    
    public function start()
    {
        $layout = new BoardLayout();
        $droid = new Droid($layout);

        $journey = $droid->letsFly();

        print_r($journey);

        return view('navigation');
    }

}
