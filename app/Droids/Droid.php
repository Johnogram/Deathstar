<?php

declare(strict_types=1);

namespace App\Droids;

use App\Boards\BoardLayout;
use App\Traits\MakeRequest;
use GuzzleHttp\Client;

class Droid
{

    // DISCONTINUE
    // This will work, but too much like brute force, hitting recurrsion issues
    // Solution will be to use the X,Y pointer and the map return from the API
    // to calculate the next step instead of trying all possible combinations
    // left and right

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

    public function letsFly() : string
    {
        $this->moveForward();

        return $this->_path;
    }

    // Try using X and Y
    // Or totally remove X and Y

    // Trying to move outside the bounds, negative left, not making it all the way right.... change the for loop to be smarter

    private function moveForward()
    {
        $this->_path = $this->_path . $this->_forward;
        $this->_y++;

        $flight = $this->makeRequest($this->_path);
        $this->navigate($flight);
    }

    private function recalculateCourse()
    {
        $this->_path = substr($this->_path, 0, -1);
        //$this->_y--;

        $steps_left = $this->alterCourseLeft($this->_left);
        $steps_right = $this->alterCourseRight($this->_right);

        $left_or_right = ($steps_left > $steps_right);

        $path_correction = ($left_or_right)
            ? str_repeat($this->_left, $steps_left)
            : str_repeat($this->_right, $steps_right);

        // print_r($steps_left);
        // print_r($steps_right);
        // print_r($left_or_right);
        // print_r($path_correction);
        // echo "\r\n";
        // echo "\r\n";

        // $this->_path = $this->_path . $path_correction . $this->_forward;
        $this->_path = $this->_path . $path_correction;
        $this->_x = ($left_or_right)
            ? $this->_x - $steps_left
            : $this->_x + $steps_right;

        // $flight = $this->makeRequest($this->_path);
        // $this->navigate($flight);


    }

    private function alterCourseLeft() : int
    {
        for ($i = $this->_x; $i >= 0; $i--) {
            $inc = $this->_x - $i;
            $path = $this->_path . str_repeat($this->_left, $inc) . $this->_forward;
            $flight = $this->makeRequest($path);

            print_r("L: " . $flight['message']);

            if ($flight['status'] === 410) {
                return $inc;
            }
        }

        return 0;
    }

    private function alterCourseRight() : int
    {
        for ($i = $this->_x; $i < 9; $i++) {
            $inc = $i - $this->_x;
            $path = $this->_path . str_repeat($this->_right, $inc) . $this->_forward;
            $flight = $this->makeRequest($path);

            print_r("R: " . $flight['message']);

            if ($flight['status'] === 410) {
                return $inc;
            }
        }

        return 0;
    }

    private function courseComplete()
    {
        return redirect()->route('complete');
    }

    private function navigate(array $flight)
    {
        print_r($this->_path . "\r\n");
        switch ($flight['status']) {
        case 410:
            $this->moveForward();
            break;

        case 417:
            if (!$this->checkCrashPosition($flight['message'])) {
                print_r($this->_path . " " . $this->_x . " " . $this->_y . " " . $flight['message']);
                throw new \Exception("Pointer co-ordinates wrong");
            }
            
            $this->recalculateCourse();
            break;

        case 200:
            $this->courseComplete();
            break;

        default:
            $this->moveForward();
            // Change to throw except
            break;
        }
    }

    private function checkCrashPosition(string $message)
    {
        $logged_position = (string) $this->_y . "," . $this->_x . ".";
        
        return (substr($message, 20) === $logged_position);
    }

}
