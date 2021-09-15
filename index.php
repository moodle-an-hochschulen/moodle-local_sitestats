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
 * Local plugin "Site statistics" - Main page
 *
 * @package     local_sitestats
 * @copyright   2019 Alexander Bias, Ulm University <alexander.bias@uni-ulm.de>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// This page can grow big, output it right away.
define('NO_OUTPUT_BUFFERING', true);

// Globals.
global $CFG, $PAGE, $OUTPUT;

// Requirements.
require_once(__DIR__ . '/../../config.php');

// Permissions check.
require_login();
require_capability('local/sitestats:view', context_system::instance());

// Initialize page.
$PAGE->set_url('/local/sitestats/index.php');
$PAGE->set_pagelayout('base');
$PAGE->set_context(context_system::instance());
$PAGE->set_heading('Site statistics');

// Page header.
echo $OUTPUT->header();

// Build tabs.
$action = optional_param('action', null, PARAM_ALPHA);
if (!in_array($action, ['viewtable', 'viewchart', 'viewmetrics', 'crawl', 'flush', 'settings'])) {
    $action = 'viewtable';
}
$tabs = [];
if (has_capability('local/sitestats:view', context_system::instance())) {
    $tabs[] = new \tabobject('viewtable', new \moodle_url($PAGE->url, ['action' => 'viewtable']), get_string('pageviewtable', 'local_sitestats'));
    $tabs[] = new \tabobject('viewchart', new \moodle_url($PAGE->url, ['action' => 'viewchart']), get_string('pageviewchart', 'local_sitestats'));
    $tabs[] = new \tabobject('viewmetrics', new \moodle_url($PAGE->url, ['action' => 'viewmetrics']), get_string('pageviewmetrics', 'local_sitestats'));
}
if (has_capability('local/sitestats:crawl', context_system::instance())) {
    $tabs[] = new \tabobject('crawl', new \moodle_url($PAGE->url, ['action' => 'crawl']), get_string('pagecrawl', 'local_sitestats'));
}
if (has_capability('local/sitestats:flush', context_system::instance())) {
    $tabs[] = new \tabobject('flush', new \moodle_url($PAGE->url, ['action' => 'flush']), get_string('pageflush', 'local_sitestats'));
}
if (has_capability('local/sitestats:settings', context_system::instance())) {
    $tabs[] = new \tabobject('settings', new \moodle_url($PAGE->url, ['action' => 'settings']), get_string('pagesettings', 'local_sitestats'));
}
$tabtree = new \tabtree($tabs, $action);
echo $OUTPUT->render($tabtree);

// Build page content.
// Page content for tab "View table".
$renderer = $PAGE->get_renderer('local_sitestats');
if ($action == 'viewtable' && has_capability('local/sitestats:view', context_system::instance())) {
    echo $renderer->render_tab_viewtable();

    // Page content for tab "View chart".
} else if ($action == 'viewchart' && has_capability('local/sitestats:view', context_system::instance())) {
    echo $renderer->render_tab_viewchart();

    // Page content for tab "View metrics".
} else if ($action == 'viewmetrics' && has_capability('local/sitestats:view', context_system::instance())) {
    echo $renderer->render_tab_viewmetrics();

    // Page content for tab "Crawl".
} else if ($action == 'crawl' && has_capability('local/sitestats:crawl', context_system::instance())) {
    echo $renderer->render_tab_crawl();

    // Page content for tab "Flush".
} else if ($action == 'flush' && has_capability('local/sitestats:flush', context_system::instance())) {
    echo $renderer->render_tab_flush();

    // Page content for tab "Settings".
} else if ($action == 'settings' && has_capability('local/sitestats:settings', context_system::instance())) {
    echo $renderer->render_tab_settings();
}

// Page footer.
echo $OUTPUT->footer();
