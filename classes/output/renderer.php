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
 * Local plugin "Site statistics" - Renderer
 *
 * @package     local_sitestats
 * @copyright   2019 Alexander Bias, Ulm University <alexander.bias@uni-ulm.de>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_sitestats\output;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/local/sitestats/locallib.php');

/**
 * The local_sitestats crawl renderer class.
 *
 * @package     local_sitestats
 * @copyright   2019 Alexander Bias, Ulm University <alexander.bias@uni-ulm.de>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class renderer extends \plugin_renderer_base {

    /**
     * Returns the content of the "View table" tab.
     *
     * @return string HTML
     */
    public function render_tab_viewtable() {
        global $DB;

        // Get config.
        $config = get_config('local_sitestats');

        // Prepare output.
        $output = '';

        // Get the sum of all crawled installations.
        $sql_sites = 'SELECT id, sitelastcrawled
             FROM {local_sitestats_sites}
             WHERE sitelastcrawled IS NOT NULL';
        $result_sites = $DB->get_records_sql($sql_sites);
        $sumofsites = count($result_sites);

        // Stop here if we have not crawled any site yet.
        if ($sumofsites == 0) {
            $output .= \html_writer::start_tag('div', array('class' => 'alert alert-info'));
            $output .= get_string('nositescrawledyet', 'local_sitestats');
            $output .= \html_writer::end_tag('div');
            return $output;
        }

        // Do only if plugin crawler is enabled.
        if ($config->crawlplugins == true) {

            // Get the sum of all crawled installations with plugins.
            $sql_siteswithplugins = 'SELECT DISTINCT site
             FROM {local_sitestats_plugins_site}';
            $result_siteswithplugins = $DB->get_records_sql($sql_siteswithplugins);
            $sumofsiteswithplugins = count($result_siteswithplugins);

            // Do only if we have found at least one site with a plugin.
            if ($sumofsiteswithplugins > 0) {

                // Get all site records and the plugin counts from DB ordered by site name
                $sql_sites = 'SELECT site.id, site.url, site.title, count(jointable.site)
                    FROM {local_sitestats_sites} AS site
                    JOIN {local_sitestats_plugins_site} AS jointable
                    ON site.id = jointable.site
                    GROUP BY site.id, site.url, site.title
                    ORDER BY title ASC';
                $result_sites = $DB->get_records_sql($sql_sites);

                // Get all plugin records and the installation counts from DB ordered by installation count
                $sql_plugins = 'SELECT pl.id, pl.frankenstyle, pl.title, count(pl.frankenstyle)
                    FROM {local_sitestats_plugins} AS pl
                    JOIN {local_sitestats_plugins_site} AS jointable
                    ON pl.id = jointable.plugin
                    GROUP BY pl.id, pl.frankenstyle, pl.title
                    ORDER BY count(pl.*) DESC, pl.frankenstyle ASC';
                $result_plugins = $DB->get_records_sql($sql_plugins);

                // Clean results from Moodle instances which have reported all plugins as installed which is simply not realistic
                // but rather some strange webserver configuration.
                $countfoundplugins = count($result_plugins);
                foreach ($result_sites as $s) {
                    if ($s->count == $countfoundplugins) {
                        // Remove site entry from list of sites.
                        unset ($result_sites[$s->id]);
                        // Decrease plugin count in list of plugins.
                        foreach ($result_plugins as $p) {
                            $currentcount = $result_plugins[$p->id]->count;
                            if ($currentcount - 1 > 0) {
                                $result_plugins[$p->id]->count = $currentcount - 1;
                            } else {
                                unset($result_plugins[$p->id]);
                            }
                        }
                    }
                }

                // Build table heading.
                $output .= \html_writer::tag('h3', get_string('table_pluginusedlabel', 'local_sitestats'));

                // Start table.
                $output .= \html_writer::start_tag('table', array('class' => 'table table-sm table-hover table-striped table-responsive'));
                // Heading.
                $output .= \html_writer::start_tag('thead');
                $output .= \html_writer::start_tag('tr');
                // Empty cell left top.
                $output .= \html_writer::tag('th', '&nbsp;');
                // Headings for all sites.
                foreach ($result_sites as $site) {
                    $output .= \html_writer::start_tag('th');
                    $output .= \html_writer::link($site->url, $site->title);
                    $output .= \html_writer::end_tag('th');
                }
                // Heading for sum.
                $output .= \html_writer::start_tag('th');
                $output .= get_string('sum', 'local_sitestats');
                $output .= \html_writer::end_tag('th');
                // End of line.
                $output .= \html_writer::end_tag('tr');
                $output .= \html_writer::end_tag('thead');

                // Plugins.
                foreach ($result_plugins as $plugin) {
                    // Get the sites using the plugin from DB
                    $sql_pluginsites = 'SELECT site.url
                        FROM {local_sitestats_sites} AS site
                        JOIN {local_sitestats_plugins_site} AS jointable
                        ON site.id = jointable.site
                        WHERE jointable.plugin = ' . $plugin->id;
                    $result_pluginsites = $DB->get_records_sql($sql_pluginsites);
                    // Table row for plugin
                    $output .= \html_writer::start_tag('tr');
                    $output .= \html_writer::start_tag('td');
                    $output .= \html_writer::link('https://moodle.org/plugins/view/' . $plugin->frankenstyle, $plugin->title);
                    $output .= \html_writer::empty_tag('br');
                    $output .= '(' . $plugin->frankenstyle . ')';
                    $output .= \html_writer::end_tag('td');
                    // One cell per site
                    foreach ($result_sites as $site) {
                        // Is plugin installed
                        if (array_key_exists($site->url, $result_pluginsites)) {
                            $output .= \html_writer::start_tag('td');
                            $output .= \html_writer::tag('span', 'Yes', array('class' => 'label label-success'));
                            $output .= \html_writer::end_tag('td');
                        } else {
                            $output .= \html_writer::start_tag('td');
                            $output .= \html_writer::tag('span', 'No', array('class' => 'label label-danger'));
                            $output .= \html_writer::end_tag('td');
                        }
                    }
                    // Sum cell
                    $output .= \html_writer::tag('td', $plugin->count);
                    $output .= \html_writer::end_tag('tr');
                }

                // Sums.
                $output .= \html_writer::start_tag('tfoot');
                $output .= \html_writer::start_tag('tr');
                $output .= \html_writer::tag('td', 'Sum of installed plugins');
                // Cells for all sites
                foreach ($result_sites as $site) {
                    $output .= \html_writer::tag('td', $site->count);
                }
                // Empty cell right bottom.
                $output .= \html_writer::tag('td', '&nbsp;');
                $output .= \html_writer::end_tag('tr');
                $output .= \html_writer::end_tag('tfoot');
                $output .= \html_writer::end_tag('table');
            }
        }

        return $output;
    }

    /**
     * Returns the content of the "View chart" tab.
     *
     * @return string HTML
     */
    public function render_tab_viewchart() {
        global $DB, $OUTPUT;

        // Get config.
        $config = get_config('local_sitestats');

        // Prepare output.
        $output = '';

        // Get the sum of all crawled installations.
        $sql_sites = 'SELECT id, sitelastcrawled
             FROM {local_sitestats_sites}
             WHERE sitelastcrawled IS NOT NULL';
        $result_sites = $DB->get_records_sql($sql_sites);
        $sumofsites = count($result_sites);

        // Stop here if we have not crawled any site yet.
        if ($sumofsites == 0) {
            $output .= \html_writer::start_tag('div', array('class' => 'alert alert-info'));
            $output .= get_string('nositescrawledyet', 'local_sitestats');
            $output .= \html_writer::end_tag('div');
            return $output;
        }

        // Do only if plugin crawler is enabled.
        if ($config->crawlplugins == true) {

            // Get the sum of all crawled installations with plugins.
            $sql_siteswithplugins = 'SELECT DISTINCT site
             FROM {local_sitestats_plugins_site}';
            $result_siteswithplugins = $DB->get_records_sql($sql_siteswithplugins);
            $sumofsiteswithplugins = count($result_siteswithplugins);

            // Do only if we have found at least one site with a plugin.
            if ($sumofsiteswithplugins > 0) {

                // Get all site records and the plugin counts from DB ordered by site name
                $sql_sites = 'SELECT site.id, site.url, site.title, count(jointable.site)
                    FROM {local_sitestats_sites} AS site
                    JOIN {local_sitestats_plugins_site} AS jointable
                    ON site.id = jointable.site
                    GROUP BY site.id, site.url, site.title
                    ORDER BY count(jointable.site) ASC';
                $result_sites = $DB->get_records_sql($sql_sites);

                // Get all plugin records and the installation counts from DB ordered by installation count
                $sql_plugins = 'SELECT pl.id, pl.frankenstyle, pl.title, count(pl.frankenstyle)
                    FROM {local_sitestats_plugins} AS pl
                    JOIN {local_sitestats_plugins_site} AS jointable
                    ON pl.id = jointable.plugin
                    GROUP BY pl.id, pl.frankenstyle, pl.title
                    ORDER BY count(pl.*) DESC, pl.frankenstyle ASC';
                $result_plugins = $DB->get_records_sql($sql_plugins);

                // Clean results from Moodle instances which have reported all plugins as installed which is simply not realistic
                // but rather some strange webserver configuration.
                $countfoundplugins = count($result_plugins);
                foreach ($result_sites as $s) {
                    if ($s->count == $countfoundplugins) {
                        // Remove site entry from list of sites.
                        unset ($result_sites[$s->id]);
                        // Decrease plugin count in list of plugins.
                        foreach ($result_plugins as $p) {
                            $currentcount = $result_plugins[$p->id]->count;
                            if ($currentcount - 1 > 0) {
                                $result_plugins[$p->id]->count = $currentcount - 1;
                            } else {
                                unset($result_plugins[$p->id]);
                            }
                        }
                    }
                }

                // Pick the most used plugins.
                $i = 0;
                $mostusedpluginsabsolutedata = array();
                $mostusedpluginslabels = array();
                foreach ($result_plugins as $p) {
                    $mostusedpluginsabsolutedata[] = $p->count;
                    $mostusedpluginslabels[] = $p->frankenstyle;
                    $i++;
                    if ($i == $config->pluginchartnumber) {
                        break;
                    }
                }

                // Build chart heading.
                $output .= \html_writer::tag('h3', get_string('chart_pluginmostusedlabel', 'local_sitestats',
                    array('number' => $config->pluginchartnumber)));

                // Build chart.
                $chart = new \core\chart_bar();
                $chart->set_horizontal(true);
                $chart->add_series(new \core\chart_series(
                        get_string('chart_pluginusedabsolutelabel', 'local_sitestats', array('number' => $sumofsites)),
                        $mostusedpluginsabsolutedata)
                );
                $chart->set_labels($mostusedpluginslabels);

                $output .= $OUTPUT->render($chart);

                // Pick the plugin counts per site.
                $pluginsusedpersiterawdata = array();
                foreach ($result_sites as $s) {
                    if ($pluginsusedpersiterawdata[$s->count]) {
                        $pluginsusedpersiterawdata[$s->count]++;
                    } else {
                        $pluginsusedpersiterawdata[$s->count] = 1;
                    };
                }
                $pluginsusedpersitedata = array();
                $pluginsusedpersitelabels = array();
                for ($j = 1; $j <= max(array_keys($pluginsusedpersiterawdata)); $j += 5) {
                    $pluginsusedpersitedata[] = $pluginsusedpersiterawdata[$j] + $pluginsusedpersiterawdata[$j + 1] +
                        $pluginsusedpersiterawdata[$j + 2] + $pluginsusedpersiterawdata[$j + 3] + $pluginsusedpersiterawdata[$j + 4];
                    $pluginsusedpersitelabels[] = get_string('chart_pluginusedpersiteaxis', 'local_sitestats', array('from' => $j, 'to' => ($j + 4)));
                }

                // Build chart heading.
                $output .= \html_writer::tag('h3', get_string('chart_pluginusedpersitelabel', 'local_sitestats'));

                // Build chart.
                $chart2 = new \core\chart_line();
                $chart2->set_smooth(true);
                $chart2->add_series(new \core\chart_series(
                        get_string('chart_pluginusedpersiteabsolutelabel', 'local_sitestats'),
                        $pluginsusedpersitedata)
                );
                $chart2->set_labels($pluginsusedpersitelabels);
                $output .= $OUTPUT->render($chart2);
            }
        }

        return $output;
    }


    /**
     * Returns the content of the "View metrics" tab.
     *
     * @return string HTML
     */
    public function render_tab_viewmetrics() {
        global $DB;

        // Get config.
        $config = get_config('local_sitestats');

        // Prepare output.
        $output = '';

        // Get the sum of all crawled installations.
        $sql_sites = 'SELECT id, sitelastcrawled
             FROM {local_sitestats_sites}
             WHERE sitelastcrawled IS NOT NULL';
        $result_sites = $DB->get_records_sql($sql_sites);
        $sumofsites = count($result_sites);

        // Stop here if we have not crawled any site yet.
        if ($sumofsites == 0) {
            $output .= \html_writer::start_tag('div', array('class' => 'alert alert-info'));
            $output .= get_string('nositescrawledyet', 'local_sitestats');
            $output .= \html_writer::end_tag('div');
            return $output;
        }

        // Do only if plugin crawler is enabled.
        if ($config->crawlplugins == true) {

            // Get the sum of all crawled installations with plugins.
            $sql_siteswithplugins = 'SELECT DISTINCT site
             FROM {local_sitestats_plugins_site}';
            $result_siteswithplugins = $DB->get_records_sql($sql_siteswithplugins);
            $sumofsiteswithplugins = count($result_siteswithplugins);

            // Do only if we have found at least one site with a plugin.
            if ($sumofsiteswithplugins > 0) {

                // Get all site records and the plugin counts from DB ordered by site name
                $sql_sites = 'SELECT site.id, site.url, site.title, count(jointable.site)
                    FROM {local_sitestats_sites} AS site
                    JOIN {local_sitestats_plugins_site} AS jointable
                    ON site.id = jointable.site
                    GROUP BY site.id, site.url, site.title
                    ORDER BY title ASC';
                $result_sites = $DB->get_records_sql($sql_sites);

                // Get all plugin records and the installation counts from DB ordered by installation count
                $sql_plugins = 'SELECT pl.id, pl.frankenstyle, pl.title, count(pl.frankenstyle)
                    FROM {local_sitestats_plugins} AS pl
                    JOIN {local_sitestats_plugins_site} AS jointable
                    ON pl.id = jointable.plugin
                    GROUP BY pl.id, pl.frankenstyle, pl.title
                    ORDER BY count(pl.*) DESC, pl.frankenstyle ASC';
                $result_plugins = $DB->get_records_sql($sql_plugins);

                // Clean results from Moodle instances which have reported all plugins as installed which is simply not realistic
                // but rather some strange webserver configuration.
                $countfoundplugins = count($result_plugins);
                foreach ($result_sites as $s) {
                    if ($s->count == $countfoundplugins) {
                        // Remove site entry from list of sites.
                        unset ($result_sites[$s->id]);
                        // Decrease plugin count in list of plugins.
                        foreach ($result_plugins as $p) {
                            $currentcount = $result_plugins[$p->id]->count;
                            if ($currentcount - 1 > 0) {
                                $result_plugins[$p->id]->count = $currentcount - 1;
                            } else {
                                unset($result_plugins[$p->id]);
                            }
                        }
                    }
                }

                // Get most plugins on a site.
                $mostpluginsonasite = 0;
                foreach ($result_sites as $site) {
                    // Check and remember most plugins on a site.
                    if ($site->count > $mostpluginsonasite) {
                        $mostpluginsonasite = $site->count;
                    }
                }

                // Get mean and median plugins per site.
                $pluginonsites = array();
                foreach ($result_sites as $site) {
                    $pluginonsites[] = $site->count;
                }
                $meanpluginspersite = local_sitestats_array_mean($pluginonsites);
                $medianpluginspersite = local_sitestats_array_median($pluginonsites);
            }
        }

        // Show metrics table.
        $output .= \html_writer::tag('h3', get_string('metrics_basedata', 'local_sitestats'));
        $output .= \html_writer::start_tag('table', array('class' => 'table table-sm table-hover table-striped table-responsive'));
        $output .= \html_writer::start_tag('tr');
        $output .= \html_writer::start_tag('td');
        $output .= get_string('metrics_basedatasitescrawled', 'local_sitestats');
        $output .= \html_writer::end_tag('td');
        $output .= \html_writer::start_tag('td');
        $output .= $sumofsites;
        $output .= \html_writer::end_tag('td');
        $output .= \html_writer::end_tag('tr');
        if ($config->crawlplugins == true) {
            $output .= \html_writer::start_tag('tr');
            $output .= \html_writer::start_tag('td');
            $output .= get_string('metrics_basedatasiteswithoutplugins', 'local_sitestats');
            $output .= \html_writer::end_tag('td');
            $output .= \html_writer::start_tag('td');
            $output .= $sumofsites - $sumofsiteswithplugins;
            $output .= \html_writer::end_tag('td');
            $output .= \html_writer::end_tag('tr');
            $output .= \html_writer::start_tag('tr');
            $output .= \html_writer::start_tag('td');
            $output .= get_string('metrics_basedatasiteswithplugins', 'local_sitestats');
            $output .= \html_writer::end_tag('td');
            $output .= \html_writer::start_tag('td');
            $output .= $sumofsiteswithplugins;
            $output .= \html_writer::end_tag('td');
            $output .= \html_writer::end_tag('tr');
            if ($meanpluginspersite > 0) {
                $output .= \html_writer::start_tag('tr');
                $output .= \html_writer::start_tag('td');
                $output .= get_string('metrics_basedatameanpluginspersite', 'local_sitestats');
                $output .= \html_writer::end_tag('td');
                $output .= \html_writer::start_tag('td');
                $output .= $meanpluginspersite;
                $output .= \html_writer::end_tag('td');
                $output .= \html_writer::end_tag('tr');
            }
            if ($medianpluginspersite > 0) {
                $output .= \html_writer::start_tag('tr');
                $output .= \html_writer::start_tag('td');
                $output .= get_string('metrics_basedatamedianpluginspersite', 'local_sitestats');
                $output .= \html_writer::end_tag('td');
                $output .= \html_writer::start_tag('td');
                $output .= $medianpluginspersite;
                $output .= \html_writer::end_tag('td');
                $output .= \html_writer::end_tag('tr');
            }
            if ($mostpluginsonasite > 0) {
                $output .= \html_writer::start_tag('tr');
                $output .= \html_writer::start_tag('td');
                $output .= get_string('metrics_basedatamostpluginsonasite', 'local_sitestats');
                $output .= \html_writer::end_tag('td');
                $output .= \html_writer::start_tag('td');
                $output .= $mostpluginsonasite;
                $output .= \html_writer::end_tag('td');
                $output .= \html_writer::end_tag('tr');
             }
        }
        $output .= \html_writer::end_tag('table');

        return $output;
    }

    /**
     * Returns the content of the "Crawl" tab.
     *
     * @return string HTML
     */
    public function render_tab_crawl() {
        global $OUTPUT;

        $url = new \moodle_url('/admin/tool/task/schedule_task.php?task=local_sitestats%5Ctask%5Ccrawl');
        $content = $OUTPUT->box($OUTPUT->single_button($url, get_string('button_runcrawltask', 'local_sitestats')), 'clearfix mdl-align');

        return $content;
    }

    /**
     * Returns the content of the "Flush" tab.
     *
     * @return string HTML
     */
    public function render_tab_flush() {
        global $OUTPUT;

        $url = new \moodle_url('/local/sitestats/flush.php');
        $content = $OUTPUT->box($OUTPUT->single_button($url, get_string('button_flushtables', 'local_sitestats')), 'clearfix mdl-align');

        return $content;
    }

    /**
     * Returns the content of the "Settings" tab.
     *
     * @return string HTML
     */
    public function render_tab_settings() {
        global $OUTPUT;

        $url = new \moodle_url('/admin/settings.php?section=sitestats&return=site');
        $content = $OUTPUT->box($OUTPUT->single_button($url, get_string('button_gotosettings', 'local_sitestats')), 'clearfix mdl-align');

        return $content;
    }
}
