<?php

declare(strict_types=1);

namespace App\Droids;

use App\Boards\BoardLayout;
use App\Traits\MakeRequest;
use GuzzleHttp\Client;

class Droid
{

    use MakeRequest;

    private $_client;

    private $_path;
    private $_layout;

    private $_x;
    private $_y;
    
    private $_forward;
    private $_left;
    private $_right;

    public function __construct(BoardLayout $layout)
    {
        $this->_client = new Client([
            'http_errors' => false,
        ]);

        $this->_path = "";
        $this->_layout = $layout;

        // Log route to return at end?

        $this->_x = 4;
        $this->_y = 0;

        $this->_forward = "f";
        $this->_left = "l";
        $this->_right = "r";
    }

    // This probably needs to be refactored to be used throughout
    public function letsFly() : string
    {
        $this->moveForward();

        return $this->_path;
    }

    // Try using X and Y

    private function moveForward()
    {
        $this->_path = $this->_path . $this->_forward;
        $this->_y ++;

        $flight = $this->makeRequest($this->_path);

        $this->navigate($flight);
    }

    private function recalculateCourse()
    {

    }

    private function courseComplete()
    {
        return redirect()->route('complete');
    }

    private function navigate(array $flight)
    {
        // Sanity check crash position

        switch ($flight['status']) {
        case 410:
            $this->moveForward();
            break;

        case 417:
            if (!$this->checkCrashPosition($flight['message'])) {
                print_r("Crash wrong");
            }
            print_r($flight);exit;
            break;

        case 200:
            $this->courseComplete();
            break;

        default:
            $this->moveForward();
            break;
        }
    }

    private function checkCrashPosition(string $message)
    {
        $logged_position = (string) $this->_y . "," . $this->_x . ".";
        
        return (substr($message, 20) === $logged_position);
    }

}
