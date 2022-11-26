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
 * Local plugin "Site statistics" - Task definition
 *
 * @package     local_sitestats
 * @copyright   2019 Alexander Bias, Ulm University <alexander.bias@uni-ulm.de>
 * @author      2021 Adrian Perez, Fernfachhochschule Schweiz (FFHS) <adrian.perez@ffhs.ch>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_sitestats\task;

defined('MOODLE_INTERNAL') || die();

/**
 * The local_sitestats crawl task class.
 *
 * @package     local_sitestats
 * @copyright   2019 Alexander Bias, Ulm University <alexander.bias@uni-ulm.de>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class crawl extends \core\task\scheduled_task {

    /**
     * Return localised task name.
     *
     * @return string
     */
    public function get_name() {
        return get_string('taskcrawl', 'local_sitestats');
    }

    /**
     * Execute scheduled task
     *
     * @return boolean
     */
    public function execute()
    {
        global $CFG, $DB;

        // Get config.
        $config = get_config('local_sitestats');

        // ************************
        // Startup: Site list
        // ************************

        // Do only if site list contains anything.
        if (!empty($config->crawlsites)) {

            // Output log.
            echo get_string('crawl_siteliststartup', 'local_sitestats') . PHP_EOL;

            // Iterate over all sites in the sites list.
            $sites = explode("\n", $config->crawlsites);
            foreach ($sites as $site) {
                // Trim setting lines.
                $site = trim($site);

                // Skip empty lines.
                if (strlen($site) == 0) {
                    continue;
                }

                // Make a new array on delimiter "|".
                $settings = explode('|', $site);

                // Clean parameters for processing.
                $site_url = clean_param($settings[0], PARAM_URL);
                if (count($settings) > 1) {
                    $site_title = clean_param($settings[1], PARAM_TEXT);
                } else {
                    $site_title = $site_url;
                }

                // Fill site table with the Moodle site if we don't know it yet.
                $site_result = $DB->get_record('local_sitestats_sites', array('url' => $site_url));
                if ($site_result === false) {
                    $site_record = new \stdClass();
                    $site_record->url = $site_url;
                    $site_record->title = $site_title;
                    $site_record->sitelastcrawled = null;
                    $site_record->pluginslastcrawled = null;
                    $ret = $DB->insert_record('local_sitestats_sites', $site_record, false);
                    if ($ret === true) {
                        // Output log.
                        echo get_string('crawl_sitelistremembersite', 'local_sitestats', array('site' => $site_title)) . PHP_EOL;
                    } else {
                        // Quit
                        echo get_string('crawl_sitelistremembersiteerror', 'local_sitestats', array('site' => $site_title)) . PHP_EOL;
                        return false;
                    }
                    unset ($ret);
                }
            }
        }


        // ************************
        // Startup: Plugin list
        // ************************

        // Do only if plugin crawler is enabled.
        if ($config->crawlplugins == true) {

            // Output log.
            echo get_string('crawl_pluginliststartup', 'local_sitestats') . PHP_EOL;

            // Init curl.
            $ch = curl_init();

            // Fetch plugin list.
            curl_setopt($ch, CURLOPT_URL, $config->pluginlist);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $curldoc = curl_exec($ch);

            // Was plugin list unreachable?
            if ($curldoc == false) {
                // Curl close.
                curl_close($ch);

                // Quit.
                echo get_string('error_pluginlistunreachable', 'local_sitestats') . PHP_EOL;
                return false;

                // Or was there an http error fetching the plugin list?
            } else if (curl_getinfo($ch, CURLINFO_HTTP_CODE) > 200) {
                // Curl close.
                curl_close($ch);

                // Quit.
                echo get_string('error_pluginlisthttperror', 'local_sitestats') . PHP_EOL;
                return false;

                // Otherwise the plugin list document should have arrived.
            } else {
                // Curl close.
                curl_close($ch);

                // Decode JSON.
                $pluginjson = json_decode($curldoc, true);

                // Is plugin list JSON broken?
                if ($pluginjson === null) {
                    // Quit
                    echo get_string('error_pluginlisjsonbroken', 'local_sitestats') . PHP_EOL;
                    return false;
                }
            }

            // Gather list of Moodle components.
            $plugincomponents = \core_component::get_plugin_types();

            // Gather list of blacklisted plugins.
            $blacklistedpluginsraw = explode("\n", $config->pluginblacklist);
            $blacklistedplugins = array();
            foreach ($blacklistedpluginsraw as $plugin) {
                // Trim setting lines.
                $plugin = trim($plugin);

                // Skip empty lines.
                if (strlen($plugin) == 0) {
                    continue;
                }

                // Clean parameters for processing.
                $plugin = clean_param($plugin, PARAM_ALPHANUMEXT);

                // Remember for further processing.
                $blacklistedplugins[] = $plugin;
            }

            // Gather list of custom plugins.
            $custompluginsraw = explode("\n", $config->plugincustomlist);
            foreach ($custompluginsraw as $plugin) {
                // Trim setting lines.
                $plugin = trim($plugin);

                // Skip empty lines.
                if (strlen($plugin) == 0) {
                    continue;
                }

                $plugin = explode('|', $plugin);

                $customplugin['name'] = $plugin[1];
                $customplugin['component'] = $plugin[0];
                $customplugin['pluginurl'] = $plugin[2];

                $pluginjson['plugins'][] = $customplugin;
            }

            // Iterate over all plugins in the plugin list.
            foreach ($pluginjson['plugins'] as $plugin_info) {
                // Generate plugin path.
                $plugin_frankenstyle = clean_param($plugin_info['component'], PARAM_COMPONENT);
                $plugin_title = clean_param($plugin_info['name'], PARAM_RAW_TRIMMED);
                $plugin_component = substr($plugin_frankenstyle, 0, strpos($plugin_frankenstyle, '_'));
                $plugin_name = substr($plugin_frankenstyle, strpos($plugin_frankenstyle, '_') + 1);
                $plugin_url = isset($plugin_info['pluginurl']) ? clean_param($plugin_info['pluginurl'], PARAM_URL) : '';

                // Skip plugin if it is blacklisted in our configuration.
                if (in_array($plugin_frankenstyle, $blacklistedplugins)) {
                    $plugin_blacklisted = true;
                } else {
                    $plugin_blacklisted = false;
                }

                // Skip plugin if we don't know this plugin type yet or maybe it's a 'other' plugin.
                if (empty($plugincomponents[$plugin_component])) {
                    continue;
                }

                $plugin_absolutepath = $plugincomponents[$plugin_component] . '/' . $plugin_name;
                $plugin_path = substr($plugin_absolutepath, strlen($CFG->dirroot));

                // Fill plugin table with the resulting Moodle plugin if we don't know it yet.
                $plugin_result = $DB->get_record('local_sitestats_plugins', array('frankenstyle' => $plugin_frankenstyle));
                if ($plugin_result === false) {
                    $plugin_record = new \stdClass();
                    $plugin_record->title = $plugin_title;
                    $plugin_record->frankenstyle = $plugin_frankenstyle;
                    $plugin_record->pluginpath = $plugin_path;
                    $plugin_record->pluginurl = $plugin_url;
                    $plugin_record->blacklisted = $plugin_blacklisted;
                    $ret = $DB->insert_record('local_sitestats_plugins', $plugin_record, false);
                    if ($ret === true) {
                        // Output log.
                        echo get_string('crawl_pluginlistrememberplugin', 'local_sitestats', array('plugin' => $plugin_frankenstyle)) . PHP_EOL;
                    } else {
                        // Quit
                        echo get_string('crawl_pluginlistrememberpluginerror', 'local_sitestats', array('plugin' => $plugin_frankenstyle)) . PHP_EOL;
                        return false;
                    }
                    unset ($ret);
                }
            }
        }


        // ************************
        // Crawl sites
        // ************************

        // Output log.
        echo get_string('crawl_crawlstart', 'local_sitestats').PHP_EOL;

        // Get sites from DB.
        $sites = $DB->get_records('local_sitestats_sites');

        // Iterate over sites.
        foreach ($sites as $site) {

            // Output log.
            echo get_string('crawl_sitestart', 'local_sitestats', array('site' => $site->title)).PHP_EOL;

            // ************************
            // Crawl: Plugin statistics
            // ************************

            // Do only if plugin crawler is enabled.
            if ($config->crawlplugins == true) {

                // Do only if the site has not been crawled for plugins for the configured amount of days.
                if (time() - $config->plugincrawlagaindelay * 3600 * 24 > (int)$site->pluginslastcrawled) {

                    // Output log.
                    echo get_string('crawl_pluginsstart', 'local_sitestats', array('site' => $site->title)).PHP_EOL;

                    // Init curl.
                    $ch = curl_init();

                    // Get plugins from DB.
                    $plugins = $DB->get_records('local_sitestats_plugins', array(), 'frankenstyle ASC');

                    // Iterate over plugins.
                    foreach ($plugins as $plugin) {

                        // Skip plugin if it is in blacklist.
                        if ($plugin->blacklisted == true) {
                            continue;
                        }

                        // Check if site has plugin installed.
                        curl_setopt ($ch, CURLOPT_URL, $site->url.'/'.$plugin->pluginpath.'/version.php');
                        curl_setopt ($ch, CURLOPT_NOBODY, true);
                        curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $config->plugincurltimeout);
                        $curldoc2 = curl_exec($ch);

                        // If plugin is installed.
                        if (curl_getinfo($ch, CURLINFO_HTTP_CODE) == 200) {
                            // Save finding to DB if we haven't recorded it yet.
                            $pluginfoundbefore_result = $DB->get_record('local_sitestats_plugins_site', array('site' => $site->id, 'plugin' => $plugin->id));
                            if ($pluginfoundbefore_result === false) {
                                $pluginfound_record = new \stdClass();
                                $pluginfound_record->site = $site->id;
                                $pluginfound_record->plugin = $plugin->id;
                                $ret = $DB->insert_record('local_sitestats_plugins_site', $pluginfound_record, false);
                                if ($ret === true) {
                                    // Output log.
                                    echo get_string('crawl_pluginfound', 'local_sitestats', array('site' => $site->title, 'plugin' => $plugin->frankenstyle)) . PHP_EOL;
                                } else {
                                    // Quit
                                    echo get_string('crawl_pluginfounderror', 'local_sitestats', array('plugin' => $plugin->frankenstyle)) . PHP_EOL;
                                    return false;
                                }
                                unset ($ret);
                            } else {
                                // Output log.
                                echo get_string('crawl_pluginfound', 'local_sitestats', array('site' => $site->title, 'plugin' => $plugin->frankenstyle)).PHP_EOL;
                            }

                            // If there was a timeout, head over to next site.
                        } else if (curl_errno($ch) == CURLE_OPERATION_TIMEOUTED) {
                            // Output log.
                            echo get_string('crawl_sitetimeout', 'local_sitestats', array('site' => $site->title)).PHP_EOL;
                            break;
                        }
                        // Otherwise we expect that the plugin is not installed.
                        else {
                            // Output log.
                            echo get_string('crawl_pluginnotfound', 'local_sitestats', array('site' => $site->title, 'plugin' => $plugin->frankenstyle)).PHP_EOL;
                        }
                    }

                    // Curl close
                    curl_close($ch);

                    // Remember the timestamp of crawling the site's plugins.
                    $siteupdate_record = new \stdClass();
                    $siteupdate_record->id = $site->id;
                    $siteupdate_record->pluginslastcrawled = time();
                    $ret = $DB->update_record('local_sitestats_sites', $siteupdate_record);
                    if ($ret === true) {
                        // Output log.
                        echo get_string('crawl_pluginsfinish', 'local_sitestats', array('site' => $site->title)).PHP_EOL;
                    } else {
                        // Quit
                        echo get_string('crawl_siterememberpluginscrawlederror', 'local_sitestats', array('site' =>  $site->title)) . PHP_EOL;
                        return false;
                    }
                    unset ($ret);
                } else {
                    // Output log.
                    echo get_string('crawl_pluginsskipped', 'local_sitestats', array('site' => $site->title)).PHP_EOL;
                }
            }

            // ************************
            // Crawl: Core statistics
            // ************************

            // Do only if core crawler is enabled.
            if ($config->crawlcore == true) {

                // Do only if the site has not been crawled for core information for the configured amount of days.
                if (time() - $config->corecrawlagaindelay * 3600 * 24 > (int)$site->corelastcrawled) {

                    // Output log.
                    echo get_string('crawl_corestart', 'local_sitestats', array('site' => $site->title)).PHP_EOL;

                    // Init curl.
                    $ch = curl_init();

                    // Check for Moodle core version.
                    curl_setopt ($ch, CURLOPT_URL, $site->url.'/lib/upgrade.txt');
                    curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $config->corecurltimeout);
                    curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
                    $curldoc3 = curl_exec($ch);

                    // If we got the file.
                    if (curl_getinfo($ch, CURLINFO_HTTP_CODE) == 200) {

                        // Find first occurence of a version number heading.
                        $versionfound = preg_match('/=== ([0-9]\.[0-9])\.?[0-9]? ===/', $curldoc3, $matches);

                        // If we found a version, process it.
                        if ($versionfound == 1) {
                            // Remember version number.
                            $coreversion = $matches[1];

                            // Create database record.
                            $coreversion_record = new \stdClass();
                            $coreversion_record->site = $site->id;
                            $coreversion_record->key = 'coreversion';
                            $coreversion_record->value = $coreversion;

                            // Check if we have recorded this information before in DB.
                            $coreversionbefore_result = $DB->get_record('local_sitestats_core', array('site' => $site->id, 'key' => get_string('coreversion', 'local_sitestats')));

                            // If there is already a record.
                            if ($coreversionbefore_result !== false) {
                                $coreversion_record->id = $coreversionbefore_result->id;
                                $ret = $DB->update_record('local_sitestats_core', $coreversion_record, false);

                                // If not.
                            } else {
                                $ret = $DB->insert_record('local_sitestats_core', $coreversion_record, false);
                            }
                            if ($ret === true) {
                                // Output log.
                                echo get_string('crawl_coreinformationfound', 'local_sitestats', array('site' => $site->title, 'key' => get_string('coreversion', 'local_sitestats'), 'value' => $coreversion)) . PHP_EOL;
                            } else {
                                // Quit
                                echo get_string('crawl_coreinformationfounderror', 'local_sitestats', array('key' => get_string('coreversion', 'local_sitestats'))) . PHP_EOL;
                                return false;
                            }
                            unset ($ret);
                        }
                        // Otherwise we have to say that we did not get this information.
                        else {
                            // Output log.
                            echo get_string('crawl_coreinformationnotfound', 'local_sitestats', array('site' => $site->title, 'key' => get_string('coreversion', 'local_sitestats'))).PHP_EOL;
                        }

                        // If there was a timeout, head over to next site.
                    } else if (curl_errno($ch) == CURLE_OPERATION_TIMEOUTED) {
                        // Output log.
                        echo get_string('crawl_sitetimeout', 'local_sitestats', array('site' => $site->title)).PHP_EOL;
                    }
                    // Otherwise we have to say that we did not get this information.
                    else {
                        // Output log.
                        echo get_string('crawl_coreinformationnotfound', 'local_sitestats', array('site' => $site->title, 'key' => get_string('coreversion', 'local_sitestats'))).PHP_EOL;
                    }

                    // Curl close
                    curl_close($ch);

                    // Remember the timestamp of crawling the site's core information.
                    $siteupdate_record = new \stdClass();
                    $siteupdate_record->id = $site->id;
                    $siteupdate_record->corelastcrawled = time();
                    $ret = $DB->update_record('local_sitestats_sites', $siteupdate_record);
                    if ($ret === true) {
                        // Output log.
                        echo get_string('crawl_corefinish', 'local_sitestats', array('site' => $site->title)).PHP_EOL;
                    } else {
                        // Quit
                        echo get_string('crawl_siteremembercorecrawlederror', 'local_sitestats', array('site' =>  $site->title)) . PHP_EOL;
                        return false;
                    }
                    unset ($ret);
                } else {
                    // Output log.
                    echo get_string('crawl_coreskipped', 'local_sitestats', array('site' => $site->title)).PHP_EOL;
                }
            }

            // Remember the timestamp of crawling the site.
            $siteupdate_record = new \stdClass();
            $siteupdate_record->id = $site->id;
            $siteupdate_record->sitelastcrawled = time();
            $ret = $DB->update_record('local_sitestats_sites', $siteupdate_record);
            if ($ret === true) {
                // Output log.
                echo get_string('crawl_sitefinish', 'local_sitestats', array('site' => $site->title)).PHP_EOL;
            } else {
                // Quit
                echo get_string('crawl_siteremembersitecrawlederror', 'local_sitestats', array('site' =>  $site->title)) . PHP_EOL;
                return false;
            }
            unset ($ret);
        }

        // Output log.
        echo get_string('crawl_crawlfinish', 'local_sitestats').PHP_EOL;

        return true;
    }
}
