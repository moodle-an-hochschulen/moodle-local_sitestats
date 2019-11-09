<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Local plugin "Site statistics" - Local library
 *
 * @package     local_sitestats
 * @copyright   2019 Alexander Bias, Ulm University <alexander.bias@uni-ulm.de>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Calculate mean of array.
 *
 * @param array $array
 * @return int
 */
function local_sitestats_array_mean($array) {
    if (!count($array)) {
        return 0;
    }

    $sum = 0;
    for ($i = 0; $i < count($array); $i++)
    {
        $sum += $array[$i];
    }

    return floor($sum / count($array));
}

/**
 * Calculate median of array.
 *
 * @param array $array
 * @return int
 */
function local_sitestats_array_median($array) {
    $count = count($array);

    if ($count == 0) {
        return 0;
    }

    sort($array);

    $middleval = floor(($count - 1) / 2);
    if ($count % 2) {
        $median = $array[$middleval];
    } else {
        $low = $array[$middleval];
        $high = $array[$middleval + 1];
        $median = (($low + $high) / 2);
    }
    return $median;
}
