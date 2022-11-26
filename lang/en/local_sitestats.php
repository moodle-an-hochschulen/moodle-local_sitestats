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
 * Local plugin "Site statistics" - Language pack
 *
 * @package     local_sitestats
 * @copyright   2019 Alexander Bias, Ulm University <alexander.bias@uni-ulm.de>
 * @author      2021 Adrian Perez, Fernfachhochschule Schweiz (FFHS) <adrian.perez@ffhs.ch>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Site statistics';
$string['button_flushtables'] = 'Flush tables';
$string['button_gotosettings'] = 'Go to settings';
$string['button_runcrawltask'] = 'Run the scheduled task for crawling';
$string['coreversion'] = 'Core version';
$string['chart_coreversionlabel'] = 'Core versions in production';
$string['chart_coreversionabsolutelabel'] = 'Installations found among {$a->number} crawled sites which gave us this data';
$string['chart_pluginmostusedlabel'] = '{$a->number} most used plugins';
$string['chart_pluginusedabsolutelabel'] = 'Installations found among {$a->number} crawled sites';
$string['chart_pluginusedpersitelabel'] = 'Plugins per site';
$string['chart_pluginusedpersiteaxis'] = '{$a->from} - {$a->to} plugins';
$string['chart_pluginusedpersiteabsolutelabel'] = 'Sites with the given amount of plugins used';
$string['crawl_corefinish'] = 'CORE FINISH: Site "{$a->site}" has been crawled for core information completely';
$string['crawl_coreinformationfounderror'] = 'Database problem when storing core information {$a->key} to database';
$string['crawl_coreinformationfound'] = 'CORE INFORMATION FOUND: Site "{$a->site}" did tell us this core information: {$a->key} = {$a->value}';
$string['crawl_coreinformationnotfound'] = 'CORE INFORMATION NOT FOUND: Site "{$a->site}" did not tell us this core information: {$a->key}';
$string['crawl_corestart'] = 'CORE START: Site "{$a->site}" is now being crawled for core information';
$string['crawl_coreskipped'] = 'CORE SKIPPED: Site "{$a->site}" has being crawled for core information recently';
$string['crawl_crawlfinish'] = 'CRAWL FINISH: Configured sites have been crawled completely';
$string['crawl_crawlstart'] = 'CRAWL START: Configured sites are now being crawled';
$string['crawl_pluginfound'] = 'PLUGIN FOUND: Site "{$a->site}" has this plugin installed: {$a->plugin}';
$string['crawl_pluginfounderror'] = 'Database problem when storing found plugin {$a->plugin} to database';
$string['crawl_pluginnotfound'] = 'PLUGIN NOT FOUND: Site "{$a->site}" has this plugin not installed: {$a->plugin}';
$string['crawl_pluginliststartup'] = 'STARTUP: Getting plugin list for later use';
$string['error_pluginlisthttperror'] = 'HTTP ERROR when fetching plugin list';
$string['error_pluginlistjsonbroken'] = 'JSON file with plugin list is broken and can\'t be processed';
$string['crawl_pluginlistrememberplugin'] = 'STARTUP: Remembering plugin {$a->plugin} for crawling';
$string['crawl_pluginlistrememberpluginerror'] = 'Database problem when remembering plugin {$a->plugin} for crawling';
$string['error_pluginlistunreachable'] = 'Server unreachable when fetching plugin list';
$string['crawl_pluginsfinish'] = 'PLUGINS FINISH: Site "{$a->site}" has been crawled for plugins completely';
$string['crawl_pluginsstart'] = 'PLUGINS START: Site "{$a->site}" is now being crawled for plugins';
$string['crawl_pluginsskipped'] = 'PLUGINS SKIPPED: Site "{$a->site}" has being crawled for plugins recently';
$string['crawl_sitelistremembersite'] = 'STARTUP: Remembering site "{$a->site}" for crawling';
$string['crawl_sitelistremembersiteerror'] = 'Database problem when remembering site {$a->site} for crawling';
$string['crawl_siteliststartup'] = 'STARTUP: Getting site list for later use';
$string['crawl_sitefinish'] = 'SITE FINISH: Site "{$a->site}" has been crawled completely';
$string['crawl_siteremembercorecrawlederror'] = 'Database problem when storing core information crawl timestamp of site {$a->site} to database';
$string['crawl_siterememberpluginscrawlederror'] = 'Database problem when storing plugins crawl timestamp of site {$a->site} to database';
$string['crawl_siteremembersitecrawlederror'] = 'Database problem when storing site crawl timestamp of site {$a->site} to database';
$string['crawl_sitestart'] = 'SITE START: Site "{$a->site}" is now being crawled';
$string['crawl_sitetimeout'] = 'SITE TIMEOUT: Site "{$a->site}" has not answered in time, skipping to next site';
$string['flush_error'] = 'Table {$a} could not be flushed for an unknown reason';
$string['flush_success'] = 'Table {$a} was successfully flushed';
$string['metrics_basedata'] = 'Moodle sites crawled';
$string['metrics_basedatasitescrawled'] = 'Moodle sites crawled';
$string['metrics_basedatameanpluginspersite'] = 'Mean plugins per site';
$string['metrics_basedatamedianpluginspersite'] = 'Median plugins per site';
$string['metrics_basedatasiteswithplugins'] = 'Moodle sites with at least one plugin';
$string['metrics_basedatasiteswithoutplugins'] = 'Moodle sites without any plugin';
$string['metrics_basedatamostpluginsonasite'] = 'Most plugins on a Moodle site';
$string['nositescrawledyet'] = 'No sites crawled yet';
$string['pagecrawl'] = 'Crawl';
$string['pageflush'] = 'Flush';
$string['pagesettings'] = 'Settings';
$string['pageviewchart'] = 'View charts';
$string['pageviewmetrics'] = 'View metrics';
$string['pageviewtable'] = 'View tables';
$string['setting_corecrawlagaindelay'] = 'Crawl again delay';
$string['setting_corecrawlagaindelay_desc'] = 'After a site has been crawled for core information and the results have been stored to database, it will not be crawled again before the configured amount of days has passed.';
$string['setting_corecurltimeout'] = 'cURL timeout';
$string['setting_corecurltimeout_desc'] = 'The timeout which is used for waiting for a response from a site when crawling for core information.';
$string['setting_crawlcore'] = 'Enable core crawler';
$string['setting_crawlcore_desc'] = 'Enabling this setting will enable the crawler to crawl the given sites for Moodle core information.';
$string['setting_crawlplugins'] = 'Enable plugin crawler';
$string['setting_crawlplugins_desc'] = 'Enabling this setting will enable the crawler to crawl the given sites for their installed plugins.';
$string['setting_crawlsites'] = 'Moodle site list';
$string['setting_crawlsites_desc'] = 'This site list will be crawled.<br />Please add one Moodle site URL per line. If you want to provide a title for the Moodle site, you can optionally add it after the URL and a pipe character.';
$string['setting_pluginblacklist'] = 'Moodle plugin blacklist';
$string['setting_pluginblacklist_desc'] = 'This plugin list will be ignored when crawling the sites for plugins, especially because they have been published in the Moodle plugin repo but are shipped with Moodle core in recent versions.<br />Please add one plugin frankenstyle name per line.';
$string['setting_pluginchartnumber'] = 'Number of plugins in chart';
$string['setting_pluginchartnumber_desc'] = 'The number of plugins which will be shown as top used plugins after crawling for plugins.';
$string['setting_plugincrawlagaindelay'] = 'Crawl again delay';
$string['setting_plugincrawlagaindelay_desc'] = 'After a site has been crawled for plugins and the results have been stored to database, it will not be crawled again before the configured amount of days has passed.';
$string['setting_plugincurltimeout'] = 'cURL timeout';
$string['setting_plugincurltimeout_desc'] = 'The timeout which is used for waiting for a response from a site when crawling for plugins.';
$string['setting_pluginlist'] = 'Moodle plugin list';
$string['setting_pluginlist_desc'] = 'The URL to the JSON file where the published Moodle plugins are listed. Unless you want to crawl the sites only for your own plugin list and know what you are doing, you don\'t need to change this.';
$string['sum'] = 'Sum';
$string['table_coreinformationlabel'] = 'Found core information';
$string['table_pluginusedlabel'] = 'Found plugin installations ordered by frequency of occurence';
$string['taskcrawl'] = 'Crawl Moodle instances for Site statistics';
$string['unknown'] = 'Unknown';
$string['sitestats:crawl'] = 'Crawl sites for statistics';
$string['sitestats:flush'] = 'Flush all site statistics data';
$string['sitestats:settings'] = 'Configure site statistics settings';
$string['sitestats:view'] = 'View site statistics data';
