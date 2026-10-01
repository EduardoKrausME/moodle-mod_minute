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

namespace mod_minute;

use stdClass;

/**
 * Report data retrieval.
 *
 * @package mod_minute
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class report_manager {
    /**
     * Fetch responses, respecting the current activity group when one is selected.
     *
     * @param stdClass $minute Activity instance.
     * @param int $groupid Group id, or 0 for all visible groups.
     * @return array
     */
    public static function get_responses(stdClass $minute, int $groupid = 0): array {
        global $DB;

        $params = ["minuteid" => $minute->id];
        $joins = "";
        $where = "r.minuteid = :minuteid";

        if ($groupid > 0) {
            $joins .= " JOIN {groups_members} gm ON gm.userid = r.userid AND gm.groupid = :groupid";
            $params["groupid"] = $groupid;
        }

        $sql = "SELECT r.*, u.firstname, u.lastname, u.firstnamephonetic, u.lastnamephonetic,
                       u.middlename, u.alternatename
                  FROM {minute_responses} r
                  JOIN {user} u ON u.id = r.userid
                  {$joins}
                 WHERE {$where}
              ORDER BY u.lastname, u.firstname, r.timemodified";

        return $DB->get_records_sql($sql, $params);
    }
}
