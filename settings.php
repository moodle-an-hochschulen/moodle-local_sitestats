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
 * @author      2021 Adrian Perez, Fernfachhochschule Schweiz (FFHS) <adrian.perez@ffhs.ch>
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
            'https://sandbox.moodledemo.net|Moodle demo instance
https://qa.moodledemo.net|Moodle QA instance',
            PARAM_RAW));

        // Create crawl plugins heading.
        $page->add(new admin_setting_heading('local_sitestats/crawlpluginsheading',
            get_string('setting_crawlplugins', 'local_sitestats', null, true),
            ''));

        // Create enable crawl plugins widget.
        $page->add(new admin_setting_configcheckbox('local_sitestats/crawlplugins',
            get_string('setting_crawlplugins', 'local_sitestats', null, true),
            get_string('setting_crawlplugins_desc', 'local_sitestats', null, true),
            true));

        // Create crawl plugins cURL timeout widget.
        $page->add(new admin_setting_configtext('local_sitestats/plugincurltimeout',
            get_string('setting_plugincurltimeout', 'local_sitestats', null, true),
            get_string('setting_plugincurltimeout_desc', 'local_sitestats', null, true),
            10));
        $page->hide_if('local_sitestats/plugincurltimeout',
            'local_sitestats/crawlplugins', 'notchecked');

        // Create pluginlist widget.
        $page->add(new admin_setting_configtext('local_sitestats/pluginlist',
            get_string('setting_pluginlist', 'local_sitestats', null, true),
            get_string('setting_pluginlist_desc', 'local_sitestats', null, true),
            'https://download.moodle.org/api/1.3/pluglist.php',
            PARAM_URL));
        $page->hide_if('local_sitestats/pluginlist',
            'local_sitestats/crawlplugins', 'notchecked');

        // Create plugin blacklist widget.
        $page->add(new admin_setting_configtextarea('local_sitestats/pluginblacklist',
            get_string('setting_pluginblacklist', 'local_sitestats', null, true),
            get_string('setting_pluginblacklist_desc', 'local_sitestats', null, true),
            'atto_recordrtc
block_calendar_month
block_course_overview
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
theme_classic
tinymce_managefiles
tool_dataprivacy
tool_lpimportcsv
tool_policy',
            PARAM_RAW));
        $page->hide_if('local_sitestats/pluginblacklist',
            'local_sitestats/crawlplugins', 'notchecked');

        // Create custom plugin widget.
        $page->add(new admin_setting_configtextarea('local_sitestats/plugincustomlist',
            get_string('setting_plugincustomlist', 'local_sitestats', null, true),
            get_string('setting_plugincustomlist_desc', 'local_sitestats', null, true),
            '',
            PARAM_RAW));
        $page->hide_if('local_sitestats/plugincustomlist',
            'local_sitestats/crawlplugins');

        // Create crawl plugins crawl again delay widget.
        $page->add(new admin_setting_configtext('local_sitestats/plugincrawlagaindelay',
            get_string('setting_plugincrawlagaindelay', 'local_sitestats', null, true),
            get_string('setting_plugincrawlagaindelay_desc', 'local_sitestats', null, true),
            5,
            PARAM_INT));
        $page->hide_if('local_sitestats/plugincrawlagaindelay',
            'local_sitestats/crawlplugins', 'notchecked');

        // Create crawl plugins chart number widget.
        $page->add(new admin_setting_configtext('local_sitestats/pluginchartnumber',
            get_string('setting_pluginchartnumber', 'local_sitestats', null, true),
            get_string('setting_pluginchartnumber_desc', 'local_sitestats', null, true),
            10,
            PARAM_INT));
        $page->hide_if('local_sitestats/pluginchartnumber',
            'local_sitestats/crawlplugins', 'notchecked');

        // Create crawl core heading.
        $page->add(new admin_setting_heading('local_sitestats/crawlcoreheading',
            get_string('setting_crawlcore', 'local_sitestats', null, true),
            ''));

        // Create enable crawl core widget.
        $page->add(new admin_setting_configcheckbox('local_sitestats/crawlcore',
            get_string('setting_crawlcore', 'local_sitestats', null, true),
            get_string('setting_crawlcore_desc', 'local_sitestats', null, true),
            true));

        // Create crawl core cURL timeout widget.
        $page->add(new admin_setting_configtext('local_sitestats/corecurltimeout',
            get_string('setting_corecurltimeout', 'local_sitestats', null, true),
            get_string('setting_corecurltimeout_desc', 'local_sitestats', null, true),
            10));
        $page->hide_if('local_sitestats/corecurltimeout',
            'local_sitestats/crawlcore', 'notchecked');

        // Create crawl core crawl again delay widget.
        $page->add(new admin_setting_configtext('local_sitestats/corecrawlagaindelay',
            get_string('setting_corecrawlagaindelay', 'local_sitestats', null, true),
            get_string('setting_corecrawlagaindelay_desc', 'local_sitestats', null, true),
            5,
            PARAM_INT));
        $page->hide_if('local_sitestats/corecrawlagaindelay',
            'local_sitestats/crawlcore', 'notchecked');
    }

    // Add settings page to navigation tree.
    $ADMIN->add('localplugins', $page);
}
