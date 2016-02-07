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
 * Local plugin "Site statistics" - Library
 *
 * @package     local_sitestats
 * @copyright   2019 Alexander Bias, Ulm University <alexander.bias@uni-ulm.de>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Add navigation item to nav drawer with Moodle's *_extend_navigation() hook.
 *
 * @param global_navigation $navigation
 */
function local_sitestats_extend_navigation(global_navigation $navigation) {
    // Create sitestats node.
    $sitestatsnode = navigation_node::create(get_string('pluginname', 'local_sitestats'),
        new moodle_url('/local/sitestats/index.php'),
        navigation_node::TYPE_CUSTOM,
        null,
        'sitestats',
        new pix_icon('i/settings', ''));

    // Show sitestats node in nav drawer.
    $sitestatsnode->showinflatnavigation = true;

    // Add the sitestats node.
    $navigation->add_node($sitestatsnode);
}
