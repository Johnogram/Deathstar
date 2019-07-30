<?php

declare(strict_types=1);

namespace App\Traits;

trait FlightControl
{

    protected function getCrashX(string $message) : int
    {
        return $this->getCoOrdinateFromString("/[,]\d+[.]/", $message);
    }

    protected function getCrashY(string $message) : int
    {
        // Step back 1 from crash
        return $this->getCoOrdinateFromString("/[[:space:]]\d+[,]/", $message) - 1;
    }

    private function getCoOrdinateFromString(string $pattern, string $string) : int
    {
        $value = 0;

        if (preg_match($pattern, $string, $matches)) {
            $value = str_replace([",", "."], ["", ""], $matches[0]);
        }

        return (int) trim($value);
    }

    protected function updatePath(string $current_path, int $steps, string $direction) : string
    {
        return $current_path . str_repeat($direction, $steps);
    }

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

    private function getNearestGap(array $open_gaps, int $x) : int
    {
        return array_reduce($open_gaps, function ($carry, $open_gap) use ($x) {
            return (abs($x - $open_gap) < abs($x - $carry))
                ? $open_gap
                : $carry;
        }, 0);
    }

    // IS THIS NEEDED?
    // protected function checkCrashPosition(string $message)
    // {
    //     $logged_position = (string) $this->_y . "," . $this->_x . ".";
        
    //     return (substr($message, 20) === $logged_position);
    // }

}
