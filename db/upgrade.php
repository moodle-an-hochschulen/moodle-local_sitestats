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
 * This file keeps track of upgrades to the "Site statistics" plugin
 *
 * @package    local_sitestats
 * @copyright  2019 Alexander Bias, Ulm University <alexander.bias@uni-ulm.de>
 * @author     2021 Adrian Perez, Fernfachhochschule Schweiz (FFHS) <adrian.perez@ffhs.ch>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Upgrade the sitestats plugin.
 *
 * @param int $oldversion
 * @return bool
 */
function xmldb_local_sitestats_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2019080108) {
        // Rename field key on table local_sitestats_core to name.
        $table = new xmldb_table('local_sitestats_core');
        $field = new xmldb_field('key', XMLDB_TYPE_CHAR, '200', null, XMLDB_NOTNULL, null, null, 'site');

        // Launch rename field key.
        $dbman->rename_field($table, $field, 'name');

        // Sitestats savepoint reached.
        upgrade_plugin_savepoint(true, 2019080108, 'local', 'sitestats');
    }

    return true;
}
