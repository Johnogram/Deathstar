<?php

declare(strict_types=1);

namespace App\Droids;

use App\Traits\MakeRequest;
use App\Traits\FlightControl;
use GuzzleHttp\Client;

class Droid
{

    use MakeRequest, FlightControl;

    private $client;

    private $path;
    private $layout;

    private $x;
    private $y;
    
    private $forward;
    private $left;
    private $right;

    public function __construct()
    {
        $this->client = new Client([
            'http_errors' => false,
        ]);

        $this->path = "";
        $this->layout = [];

        $this->x = 4;
        $this->y = 0;

        $this->forward = "f";
        $this->left = "l";
        $this->right = "r";
    }

    /**
     * Begin our flight and return the path when finished
     *
     * @return array The path and layout of our flight
     */
    public function letsFly() : array
    {
        $this->moveForward();

        return [
            $this->path,
            $this->layout,
        ];
    }

    /**
     * Move forward until we crash or reach our destination
     *
     * @return void
     */
    private function moveForward() : void
    {
        // Moving forward one square at a time causes too much recursion
        // 512 characters is not an unreasonable payload to deliver to an API
        $path = $this->path . str_repeat($this->forward, 512);

        $flight = $this->makeRequest($path);

        $this->navigate($flight);
    }

    /**
     * Get the information from our crash to use in re-plotting our route
     * and update our current information
     *
     * @param array $flight The API response
     *
     * @return void
     */
    private function analyseCrash(array $flight) : void
    {
        $previousy = $this->y;

        $this->x = $this->getCrashX($flight['message']);
        $this->y = $this->getCrashY($flight['message']);

        $stepsforward = $this->y - $previousy;

        $this->path = $this->updatePath($this->path, $stepsforward, $this->forward);

        $this->recalculateCourse($flight);
    }

    /**
     * Reculate our route based on the response from the API, getting the nearest gap
     * and the change required to reach it
     *
     * @param array $flight The API response
     *
     * @return void
     */
    private function recalculateCourse(array $flight) : void
    {
        [$lateral_steps, $lateral_direction, $nearest_gap] = $this->traverseObstacle(
            $flight['map'],
            $this->x,
            $this->left,
            $this->right
        );

        $this->x = $nearest_gap;

        $this->path = $this->updatePath($this->path, $lateral_steps, $lateral_direction);

        $this->moveForward();
    }

    /**
     * Once we have completed our course, log the map of the path taken
     * and clean up our path, co-ordinates, etc...
     *
     * @param array $flight The API response
     *
     * @return void
     */
    private function courseComplete(array $flight) : void
    {
        $this->layout = $flight['map'];

        // As I'm batching forward movements into 512 groups, we
        // most likely overshot the desination by a few hundred
        // But we can calculate an accurate path from the length of the map returned
        $stepsforward = (int) count($flight['map']) - $this->y;
        $this->path = $this->path . str_repeat($this->forward, $stepsforward);
    }

    /**
     * Choose the course of action based on the API response
     *
     * @param array $flight The API response
     *
     * @return void
     *
     * @throws Exception Throw an exception if we get an un-accounted for response from the API
     */
    private function navigate(array $flight) : void
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
