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
 * Response persistence and validation.
 *
 * @package mod_minute
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class submission_manager {
    /**
     * Save the current user's response.
     *
     * @param \stdClass $minute Activity instance.
     * @param \context_module $context Module context.
     * @param string $response Response text.
     * @param float|null $latitude Latitude.
     * @param float|null $longitude Longitude.
     * @param float|null $accuracy Browser-reported location accuracy.
     * @return \stdClass
     */
    public static function save(
        \stdClass $minute,
        \context_module $context,
        string $response,
        ?float $latitude,
        ?float $longitude,
        ?float $accuracy
    ): \stdClass {
        global $DB, $USER;

        require_capability("mod/minute:submit", $context);

        if (!access_manager::is_open($minute)) {
            throw new \moodle_exception("submissionclosed", "mod_minute");
        }

        $response = trim(clean_param($response, PARAM_TEXT));
        if ($response === "") {
            throw new \moodle_exception("responseempty", "mod_minute");
        }
        if (\core_text::strlen($response) > (int)$minute->maxchars) {
            throw new \moodle_exception("responsetoolong", "mod_minute", "", (int)$minute->maxchars);
        }

        self::validate_coordinates($latitude, $longitude, $accuracy);
        access_manager::validate_presence($minute, $latitude, $longitude);

        if (empty($minute->requirelocation)) {
            $latitude = null;
            $longitude = null;
            $accuracy = null;
        }
        $ipaddress = !empty($minute->requireip) ? getremoteaddr() : "";

        $now = time();
        $record = $DB->get_record("minute_responses", [
            "minuteid" => $minute->id,
            "userid" => $USER->id,
        ]);

        if ($record) {
            $record->response = $response;
            $record->ipaddress = $ipaddress;
            $record->latitude = $latitude;
            $record->longitude = $longitude;
            $record->accuracy = $accuracy;
            $record->timemodified = $now;
            $DB->update_record("minute_responses", $record);
            $created = false;
        } else {
            $record = (object)[
                "minuteid" => $minute->id,
                "userid" => $USER->id,
                "response" => $response,
                "ipaddress" => $ipaddress,
                "latitude" => $latitude,
                "longitude" => $longitude,
                "accuracy" => $accuracy,
                "timecreated" => $now,
                "timemodified" => $now,
            ];
            $record->id = $DB->insert_record("minute_responses", $record);
            $created = true;
        }

        $eventclass = $created
            ? \mod_minute\event\response_submitted::class
            : \mod_minute\event\response_updated::class;
        $event = $eventclass::create([
            "objectid" => $record->id,
            "context" => $context,
        ]);
        $event->add_record_snapshot("minute", $minute);
        $event->trigger();

        return $record;
    }

    /**
     * Get one user's response.
     *
     * @param int $minuteid Activity id.
     * @param int $userid User id.
     * @return \stdClass|null
     */
    public static function get_for_user(int $minuteid, int $userid): ?\stdClass {
        global $DB;

        $record = $DB->get_record("minute_responses", ["minuteid" => $minuteid, "userid" => $userid]);
        return $record ?: null;
    }

    /**
     * Validate coordinate values.
     *
     * @param float|null $latitude Latitude.
     * @param float|null $longitude Longitude.
     * @param float|null $accuracy Accuracy.
     * @return void
     */
    private static function validate_coordinates(?float $latitude, ?float $longitude, ?float $accuracy): void {
        if ($latitude !== null && ($latitude < -90 || $latitude > 90)) {
            throw new \moodle_exception("errorlatitude", "mod_minute");
        }
        if ($longitude !== null && ($longitude < -180 || $longitude > 180)) {
            throw new \moodle_exception("errorlongitude", "mod_minute");
        }
        if ($accuracy !== null && $accuracy < 0) {
            throw new \moodle_exception("erroraccuracy", "mod_minute");
        }
    }
}
