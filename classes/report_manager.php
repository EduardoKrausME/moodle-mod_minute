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
     * Fetch responses, respecting activity group visibility.
     *
     * @param stdClass $minute Activity instance.
     * @param int|int[] $groupids Group id, allowed group ids, or 0 for all groups.
     * @return array
     */
    public static function get_responses(stdClass $minute, int|array $groupids = 0): array {
        global $DB;

        $params = ["minuteid" => $minute->id];
        $where = "r.minuteid = :minuteid";

        if (is_array($groupids)) {
            $groupids = array_values(array_filter(array_map("intval", $groupids)));
            if (!$groupids) {
                return [];
            }

            [$insql, $inparams] = $DB->get_in_or_equal($groupids, SQL_PARAMS_NAMED, "group");
            $where .= " AND EXISTS (
                SELECT 1
                  FROM {groups_members} gm
                 WHERE gm.userid = r.userid
                   AND gm.groupid {$insql}
            )";
            $params += $inparams;
        } else if ($groupids > 0) {
            $where .= " AND EXISTS (
                SELECT 1
                  FROM {groups_members} gm
                 WHERE gm.userid = r.userid
                   AND gm.groupid = :groupid
            )";
            $params["groupid"] = $groupids;
        }

        $sql = "SELECT r.*, u.firstname, u.lastname, u.firstnamephonetic, u.lastnamephonetic,
                       u.middlename, u.alternatename
                  FROM {minute_responses} r
                  JOIN {user} u ON u.id = r.userid
                 WHERE {$where}
              ORDER BY u.lastname, u.firstname, r.timemodified";

        return $DB->get_records_sql($sql, $params);
    }
}
