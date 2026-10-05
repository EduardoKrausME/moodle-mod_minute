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
 * Restore structure step for mod_minute.
 *
 * @package mod_minute
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restore_minute_activity_structure_step extends restore_activity_structure_step {
    /**
     * Define restore paths.
     *
     * @return restore_path_element[]
     */
    protected function define_structure() {
        $paths = [
            new restore_path_element("minute", "/activity/minute"),
        ];

        if ($this->get_setting_value("userinfo")) {
            $paths[] = new restore_path_element("minute_response", "/activity/minute/responses/response");
        }

        return $this->prepare_activity_structure($paths);
    }

    /**
     * Restore the activity record.
     *
     * @param array $data Restored data.
     * @return void
     */
    protected function process_minute($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        $data->course = $this->get_courseid();

        if (!empty($data->timestart)) {
            $data->timestart = $this->apply_date_offset($data->timestart);
        }
        if (!empty($data->timeend)) {
            $data->timeend = $this->apply_date_offset($data->timeend);
        }

        if (!property_exists($data, "teacherip")) {
            $data->requireip = 0;
            $data->teacherip = "";
            $data->teacheripuserid = null;
        } else if (!empty($data->teacheripuserid)) {
            $data->teacheripuserid = $this->get_mappingid("user", $data->teacheripuserid);
            if (!$data->teacheripuserid) {
                $data->requireip = 0;
                $data->teacherip = "";
                $data->teacheripuserid = null;
            }
        } else {
            $data->teacheripuserid = null;
        }

        if (!property_exists($data, "referencelat") || !property_exists($data, "referencelon")) {
            $data->requirelocation = 0;
            $data->referencelat = null;
            $data->referencelon = null;
            $data->referencelocationuserid = null;
        } else if (!empty($data->referencelocationuserid)) {
            $data->referencelocationuserid = $this->get_mappingid("user", $data->referencelocationuserid);
            if (!$data->referencelocationuserid) {
                $data->requirelocation = 0;
                $data->referencelat = null;
                $data->referencelon = null;
                $data->referencelocationuserid = null;
            }
        } else {
            $data->referencelocationuserid = null;
        }

        $newitemid = $DB->insert_record("minute", $data);
        $this->apply_activity_instance($newitemid);
        $this->set_mapping("minute", $oldid, $newitemid, true);
    }

    /**
     * Restore one participant response.
     *
     * @param array $data Restored data.
     * @return void
     */
    protected function process_minute_response($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        $data->minuteid = $this->get_new_parentid("minute");
        $data->userid = $this->get_mappingid("user", $data->userid);

        if (!$data->userid) {
            return;
        }

        $newitemid = $DB->insert_record("minute_responses", $data);
        $this->set_mapping("minute_response", $oldid, $newitemid);
    }

    /**
     * No activity files are stored.
     *
     * @return void
     */
    protected function after_execute() {
    }
}
