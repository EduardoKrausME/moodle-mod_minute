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
 * Upgrade file.
 *
 * @package    mod_minute
 * @copyright  2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Upgrade steps for minute.
 *
 * @param int $oldversion
 * @return bool
 */
function xmldb_minute_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2026100501) {
        $table = new xmldb_table("minute");

        $field = new xmldb_field(
            "teacheripuserid",
            XMLDB_TYPE_INTEGER,
            "10",
            null,
            null,
            null,
            null,
            "teacherip"
        );
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field(
            "referencelocationuserid",
            XMLDB_TYPE_INTEGER,
            "10",
            null,
            null,
            null,
            null,
            "referencelon"
        );
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $key = new xmldb_key("teacheripuser", XMLDB_KEY_FOREIGN, ["teacheripuserid"], "user", ["id"]);
        $dbman->add_key($table, $key);

        $key = new xmldb_key(
            "referencelocationuser",
            XMLDB_KEY_FOREIGN,
            ["referencelocationuserid"],
            "user",
            ["id"]
        );
        $dbman->add_key($table, $key);

        upgrade_mod_savepoint(true, 2026100501, "minute");
    }

    return true;
}
