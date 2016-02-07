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
 * Local plugin "Site statistics" - Flush action
 *
 * @package     local_sitestats
 * @copyright   2019 Alexander Bias, Ulm University <alexander.bias@uni-ulm.de>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Requirements.
require_once(__DIR__ . '/../../config.php');

// Permissions check.
require_capability('local/sitestats:flush', context_system::instance());

// Initialize page.
$PAGE->set_url('/local/sitestats/flush.php');
$PAGE->set_pagelayout('base');
$PAGE->set_context(context_system::instance());
$PAGE->set_heading('Site statistics: Flush');

// Page header.
echo $OUTPUT->header();

// Action: Flush local_sitestats_sites table.
$ret1 = $DB->delete_records('local_sitestats_sites');
if ($ret1 == true) {
    \core\notification::success(get_string('flush_success', 'local_sitestats', 'local_sitestats_sites'));
} else {
    \core\notification::error(get_string('flush_error', 'local_sitestats', 'local_sitestats_sites'));
}

// Action: Flush local_sitestats_plugins table.
$ret2 = $DB->delete_records('local_sitestats_plugins');
if ($ret2 == true) {
    \core\notification::success(get_string('flush_success', 'local_sitestats', 'local_sitestats_plugins'));
} else {
    \core\notification::error(get_string('flush_error', 'local_sitestats', 'local_sitestats_plugins'));
}

// Action: Flush local_sitestats_plugins_site table.
$ret3 = $DB->delete_records('local_sitestats_plugins_site');
if ($ret3 == true) {
    \core\notification::success(get_string('flush_success', 'local_sitestats', 'local_sitestats_plugins_site'));
} else {
    \core\notification::error(get_string('flush_error', 'local_sitestats', 'local_sitestats_plugins_site'));
}

// Back button.
$backurl = new \moodle_url('/local/sitestats/index.php');
echo $OUTPUT->box($OUTPUT->single_button($backurl, get_string('back')), 'clearfix mdl-align');


// Page footer.
echo $OUTPUT->footer();
