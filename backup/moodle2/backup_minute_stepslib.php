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
 * Backup structure step for mod_minute.
 *
 * @package mod_minute
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class backup_minute_activity_structure_step extends backup_activity_structure_step {
    /**
     * Define the activity backup structure.
     *
     * @return backup_nested_element
     */
    protected function define_structure() {
        $userinfo = $this->get_setting_value("userinfo");

        $minute = new backup_nested_element("minute", ["id"], [
            "name",
            "intro",
            "introformat",
            "prompt",
            "timestart",
            "timeend",
            "maxchars",
            "requireip",
            "teacherip",
            "requirelocation",
            "referencelat",
            "referencelon",
            "radiusmeters",
            "timecreated",
            "timemodified",
        ]);

        $responses = new backup_nested_element("responses");
        $response = new backup_nested_element("response", ["id"], [
            "userid",
            "response",
            "ipaddress",
            "latitude",
            "longitude",
            "accuracy",
            "timecreated",
            "timemodified",
        ]);

        $minute->add_child($responses);
        $responses->add_child($response);

        $minute->set_source_table("minute", ["id" => backup::VAR_ACTIVITYID]);
        if ($userinfo) {
            $response->set_source_table("minute_responses", ["minuteid" => backup::VAR_PARENTID]);
        }

        $response->annotate_ids("user", "userid");

        return $this->prepare_activity_structure($minute);
    }
}
