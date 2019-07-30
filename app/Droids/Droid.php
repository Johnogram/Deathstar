<?php

declare(strict_types=1);

namespace App\Droids;

use App\Traits\{MakeRequest, FlightControl};
use GuzzleHttp\Client;

class Droid
{

    use MakeRequest, FlightControl;

    private $_client;

    private $_path;
    private $_layout;

    private $_x;
    private $_y;
    
    private $_forward;
    private $_left;
    private $_right;

    public function __construct()
    {
        $this->_client = new Client([
            'http_errors' => false,
        ]);

        $this->_path = "";
        $this->_layout = [];

        $this->_x = 4;
        $this->_y = 0;

        $this->_forward = "f";
        $this->_left = "l";
        $this->_right = "r";
    }

    public function letsFly()
    {
        $this->moveForward();

        return [
            $this->_path,
            $this->_layout,
        ];
    }

    private function moveForward()
    {
        // Moving forward one square at a time causes too much recursion
        // 512 characters is not an unreasonable payload to deliver to an API
        $path = $this->_path . str_repeat($this->_forward, 512);

        $flight = $this->makeRequest($path);

        $this->navigate($flight);
    }

    private function analyseCrash($flight)
    {
        $previous_y = $this->_y;

        $this->_x = $this->getCrashX($flight['message']);
        $this->_y = $this->getCrashY($flight['message']); 

        $steps_forward = $this->_y - $previous_y;

        $this->_path = $this->updatePath($this->_path, $steps_forward, $this->_forward);

        $this->recalculateCourse($flight);
    }

    private function recalculateCourse($flight)
    {
        [$lateral_steps, $lateral_direction, $nearest_gap] = $this->traverseObstacle(
            $flight['map'],
            $this->_x,
            $this->_left,
            $this->_right
        );

        $this->_x = $nearest_gap;

        $this->_path = $this->updatePath($this->_path, $lateral_steps, $lateral_direction);

        $this->moveForward();
    }

    private function courseComplete($flight)
    {
        $this->_layout = $flight['map'];

        // As I'm batching forward movements into 512 groups, we
        // most likely overshot the desination by a few hundred
        // But we can calculate an accurate path from the length of the map returned
        $steps_forward = (int) count($flight['map']) - $this->_y;
        $this->_path = $this->_path . str_repeat($this->_forward, $steps_forward);
    }

    private function navigate(array $flight)
    {
        switch ($flight['status']) {
        case 410:
            $this->moveForward();
            break;

        case 417:
            $this->analyseCrash($flight);
            break;

        case 200:
            $this->courseComplete($flight);
            break;

        default:
            throw new \Exception("Something went wrong, API response unexpected");
            break;
        }
    }

}
