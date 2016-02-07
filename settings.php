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
 * Local plugin "Site statistics" - Settings
 *
 * @package     local_sitestats
 * @copyright   2019 Alexander Bias, Ulm University <alexander.bias@uni-ulm.de>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    // New settings page.
    $page = new admin_settingpage('sitestats', get_string('pluginname', 'local_sitestats', null, true));

    if ($ADMIN->fulltree) {

        // Create crawl sites heading.
        $page->add(new admin_setting_heading('local_sitestats/crawlsitesheading',
                get_string('setting_crawlsites', 'local_sitestats', null, true),
                ''));

        // Create crawl sites widget.
        $page->add(new admin_setting_configtextarea('local_sitestats/crawlsites',
                get_string('setting_crawlsites', 'local_sitestats', null, true),
                get_string('setting_crawlsites_desc', 'local_sitestats', null, true),
                'https://demo.moodle.net|Moodle demo instance
https://qa.moodle.net|Moodle QA instance',
                PARAM_RAW));

        // Create plugin statistics heading.
        $page->add(new admin_setting_heading('local_sitestats/pluginstatisticsheading',
                get_string('setting_pluginstatistics', 'local_sitestats', null, true),
                ''));

        // Create enable plugin statistics widget.
        $page->add(new admin_setting_configcheckbox('local_sitestats/pluginstatistics',
                get_string('setting_pluginstatistics', 'local_sitestats', null, true),
                get_string('setting_pluginstatistics_desc', 'local_sitestats', null, true),
                true));

        // Create plugin statistics cURL timeout widget.
        $page->add(new admin_setting_configtext('local_sitestats/plugincurltimeout',
                get_string('setting_plugincurltimeout', 'local_sitestats', null, true),
                get_string('setting_plugincurltimeout_desc', 'local_sitestats', null, true),
                10));
        $page->hide_if('local_sitestats/plugincurltimeout',
            'local_sitestats/pluginstatistics', 'notchecked');

        // Create pluginlist widget.
        $page->add(new admin_setting_configtext('local_sitestats/pluginlist',
                get_string('setting_pluginlist', 'local_sitestats', null, true),
                get_string('setting_pluginlist_desc', 'local_sitestats', null, true),
                'https://download.moodle.org/api/1.3/pluglist.php',
                PARAM_URL));
        $page->hide_if('local_sitestats/pluginlist',
            'local_sitestats/pluginstatistics', 'notchecked');

        // Create plugin blacklist widget.
        $page->add(new admin_setting_configtextarea('local_sitestats/pluginblacklist',
            get_string('setting_pluginblacklist', 'local_sitestats', null, true),
            get_string('setting_pluginblacklist_desc', 'local_sitestats', null, true),
            'atto_recordrtc
block_calendar_month
format_singleactivity
gradereport_history
message_airnotifier
mod_book
qtype_ddimageortext
qtype_ddmarker
qtype_ddwtos
qtype_gapselect
quizaccess_offlineattempts
repository_areafiles
repository_skydrive
tinymce_managefiles
tool_dataprivacy
tool_lpimportcsv
tool_policy',
            PARAM_RAW));
        $page->hide_if('local_sitestats/pluginblacklist',
            'local_sitestats/pluginstatistics', 'notchecked');

        // Create plugin statistics crawl again delay widget.
        $page->add(new admin_setting_configtext('local_sitestats/plugincrawlagaindelay',
            get_string('setting_plugincrawlagaindelay', 'local_sitestats', null, true),
            get_string('setting_plugincrawlagaindelay_desc', 'local_sitestats', null, true),
            5,
            PARAM_INT));
        $page->hide_if('local_sitestats/plugincrawlagaindelay',
            'local_sitestats/pluginstatistics', 'notchecked');

        // Create plugin statistics chart number widget.
        $page->add(new admin_setting_configtext('local_sitestats/pluginchartnumber',
            get_string('setting_pluginchartnumber', 'local_sitestats', null, true),
            get_string('setting_pluginchartnumber_desc', 'local_sitestats', null, true),
            10,
            PARAM_INT));
        $page->hide_if('local_sitestats/pluginchartnumber',
            'local_sitestats/pluginstatistics', 'notchecked');
    }

    // Add settings page to navigation tree.
    $ADMIN->add('localplugins', $page);
}
