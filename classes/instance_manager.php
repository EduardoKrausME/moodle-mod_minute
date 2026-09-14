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
     * @param \stdClass $data Form data.
     * @return int
     */
    public static function add(\stdClass $data): int {
        global $DB;

        $now = time();
        $data->timecreated = $now;
        $data->timemodified = $now;
        $data->teacherip = self::normalise_teacher_ip($data);
        self::normalise_location($data);

        return (int)$DB->insert_record("minute", $data);
    }

    /**
     * Update an instance.
     *
     * @param \stdClass $data Form data.
     * @return bool
     */
    public static function update(\stdClass $data): bool {
        global $DB;

        $data->id = $data->instance;
        $data->timemodified = time();
        $data->teacherip = self::normalise_teacher_ip($data);
        self::normalise_location($data);

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
     * @param \stdClass $data Form data.
     * @return string
     */
    private static function normalise_teacher_ip(\stdClass $data): string {
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
     * Clear irrelevant location values when location is disabled.
     *
     * @param \stdClass $data Form data.
     * @return void
     */
    private static function normalise_location(\stdClass $data): void {
        if (empty($data->requirelocation)) {
            $data->referencelat = null;
            $data->referencelon = null;
        }
    }
}
