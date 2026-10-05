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
 * Activity instance persistence.
 *
 * @package mod_minute
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class instance_manager {
    /**
     * Add an instance.
     *
     * @param stdClass $data Form data.
     * @return int
     */
    public static function add(stdClass $data): int {
        global $DB, $USER;

        $now = time();
        $data->timecreated = $now;
        $data->timemodified = $now;
        $data->teacherip = self::normalise_teacher_ip($data);
        self::normalise_location($data);
        $data->teacheripuserid = $data->teacherip !== "" ? $USER->id : null;
        $data->referencelocationuserid = $data->referencelat !== null && $data->referencelon !== null
            ? $USER->id
            : null;

        return (int)$DB->insert_record("minute", $data);
    }

    /**
     * Update an instance.
     *
     * @param stdClass $data Form data.
     * @return bool
     */
    public static function update(stdClass $data): bool {
        global $DB, $USER;

        $current = $DB->get_record("minute", ["id" => $data->instance], "*", MUST_EXIST);
        $data->id = $data->instance;
        $data->timemodified = time();
        $data->teacherip = self::normalise_teacher_ip($data);
        self::normalise_location($data);
        self::set_reference_owners($data, $current, $USER->id);

        return $DB->update_record("minute", $data);
    }

    /**
     * Delete an instance and its responses.
     *
     * @param int $id Instance id.
     * @return bool
     */
    public static function delete(int $id): bool {
        global $DB;

        if (!$DB->record_exists("minute", ["id" => $id])) {
            return false;
        }

        $transaction = $DB->start_delegated_transaction();
        $DB->delete_records("minute_responses", ["minuteid" => $id]);
        $DB->delete_records("minute", ["id" => $id]);
        $transaction->allow_commit();

        return true;
    }

    /**
     * Ensure the teacher IP is usable when IP restriction is enabled.
     *
     * @param stdClass $data Form data.
     * @return string
     */
    private static function normalise_teacher_ip(stdClass $data): string {
        if (empty($data->requireip)) {
            return "";
        }

        $ip = trim((string)($data->teacherip ?? ""));
        if ($ip === "") {
            $ip = getremoteaddr();
        }

        return $ip;
    }

    /**
     * Attribute personal reference values to the user who set them.
     *
     * @param stdClass $data New form data.
     * @param stdClass $current Current activity record.
     * @param int $userid User saving the activity.
     * @return void
     */
    private static function set_reference_owners(stdClass $data, stdClass $current, int $userid): void {
        if ($data->teacherip === "") {
            $data->teacheripuserid = null;
        } else if (empty($current->teacheripuserid) || $data->teacherip !== $current->teacherip) {
            $data->teacheripuserid = $userid;
        } else {
            $data->teacheripuserid = $current->teacheripuserid;
        }

        $haslocation = $data->referencelat !== null && $data->referencelon !== null;
        if (!$haslocation) {
            $data->referencelocationuserid = null;
            return;
        }

        $locationchanged = $current->referencelat === null
            || $current->referencelon === null
            || (string)$data->referencelat !== (string)$current->referencelat
            || (string)$data->referencelon !== (string)$current->referencelon;
        if (empty($current->referencelocationuserid) || $locationchanged) {
            $data->referencelocationuserid = $userid;
        } else {
            $data->referencelocationuserid = $current->referencelocationuserid;
        }
    }

    /**
     * Clear irrelevant location values when location is disabled.
     *
     * @param stdClass $data Form data.
     * @return void
     */
    private static function normalise_location(stdClass $data): void {
        if (empty($data->requirelocation)) {
            $data->referencelat = null;
            $data->referencelon = null;
        }
    }
}
