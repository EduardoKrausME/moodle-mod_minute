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

use context_module;
use moodle_exception;
use stdClass;

/**
 * Teacher presence reference management.
 *
 * @package mod_minute
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class presence_manager {
    /**
     * Update the activity reference IP from the current request.
     *
     * @param stdClass $minute Activity instance.
     * @param context_module $context Module context.
     * @return string New IP.
     */
    public static function capture_ip(stdClass $minute, context_module $context): string {
        global $DB;

        require_capability("mod/minute:viewreport", $context);
        $ip = getremoteaddr();
        $DB->set_field("minute", "teacherip", $ip, ["id" => $minute->id]);
        $DB->set_field("minute", "timemodified", time(), ["id" => $minute->id]);

        return $ip;
    }

    /**
     * Update the activity reference location.
     *
     * @param stdClass $minute Activity instance.
     * @param context_module $context Module context.
     * @param float $latitude Latitude.
     * @param float $longitude Longitude.
     * @return void
     */
    public static function capture_location(
        stdClass       $minute,
        context_module $context,
        float           $latitude,
        float           $longitude
    ): void {
        global $DB;

        require_capability("mod/minute:viewreport", $context);
        if ($latitude < -90 || $latitude > 90) {
            throw new moodle_exception("errorlatitude", "mod_minute");
        }
        if ($longitude < -180 || $longitude > 180) {
            throw new moodle_exception("errorlongitude", "mod_minute");
        }

        $DB->set_field("minute", "referencelat", $latitude, ["id" => $minute->id]);
        $DB->set_field("minute", "referencelon", $longitude, ["id" => $minute->id]);
        $DB->set_field("minute", "timemodified", time(), ["id" => $minute->id]);
    }
}
