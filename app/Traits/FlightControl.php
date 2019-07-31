<?php

declare(strict_types=1);

namespace App\Traits;

trait FlightControl
{

    /**
     * Calculate the X crash co-ordinate from the response message
     *
     * @param string $message The response message
     *
     * @return integer The X co-ordinate
     */
    protected function getCrashX(string $message) : int
    {
        return $this->getCoOrdinateFromString("/[,]\d+[.]/", $message);
    }

    /**
     * Calculate the Y crash co-ordinate from the response message
     *
     * @param string $message The response message
     *
     * @return integer The Y co-ordinate
     */
    protected function getCrashY(string $message) : int
    {
        // Take 1 step back from crash
        return $this->getCoOrdinateFromString("/[[:space:]]\d+[,]/", $message) - 1;
    }

    /**
     * Get the number from the given string using the given RegEx pattern
     * Remove surrounding white space, comma and period
     *
     * @param string $pattern The RegEx pattern to use
     * @param string $string The string to check in
     *
     * @return integer The co-ordinate
     */
    private function getCoOrdinateFromString(string $pattern, string $string) : int
    {
        $value = 0;

        if (preg_match($pattern, $string, $matches)) {
            $value = str_replace([",", "."], ["", ""], $matches[0]);
        }

        return (int) trim($value);
    }

    /**
     * Update the path string, taking the current path an appending the direction on
     * a given ammount of times
     *
     * @param string $current_path The current flight path
     * @param integer $steps The number of steps taken in a given direction
     * @param string $direction Left or Right
     *
     * @return string The updated path
     */
    protected function updatePath(string $current_path, int $steps, string $direction) : string
    {
        return $current_path . str_repeat($direction, $steps);
    }

    /**
     * Calculate the change of course needed to progress based on the map returned
     *
     * @param array $map The current map we have travelled
     * @param integer $x The current X co-ordinate we are at
     * @param string $left The string used to represent left
     * @param string $right The string used to represent right
     *
     * @return array The number of steps taken, the direction and the position of the nearest gap
     */
    protected function traverseObstacle(array $map, int $x, string $left, string $right) : array
    {
        $next_row_layout = str_split($map[0]);

        $open_gaps = array_filter($next_row_layout, function ($col) {
            return $col === " ";
        });
        $open_gaps = array_keys($open_gaps);

        $nearest_gap = $this->getNearestGap($open_gaps, $x);
        $steps = $nearest_gap - $x;

        return [
            abs($steps),
            ($steps < 0) ? $left : $right,
            $nearest_gap,
        ];
    }

    /**
     * Get the position of the nearest gap
     *
     * @param array $open_gaps The positions of the open gaps
     * @param integer $x The current X co-ordinate we are at
     *
     * @return integer The position of the nearest gap
     */
    private function getNearestGap(array $open_gaps, int $x) : int
    {
        return array_reduce($open_gaps, function ($carry, $open_gap) use ($x) {
            return (abs($x - $open_gap) < abs($x - $carry))
                ? $open_gap
                : $carry;
        }, 0);
    }
}
