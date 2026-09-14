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
 * Time, IP and geolocation access rules.
 *
 * @package mod_minute
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class access_manager {
    /**
     * Whether responses are currently accepted.
     *
     * @param \stdClass $minute Activity instance.
     * @param int|null $now Timestamp.
     * @return bool
     */
    public static function is_open(\stdClass $minute, ?int $now = null): bool {
        $now ??= time();

        if (!empty($minute->timestart) && $now < (int)$minute->timestart) {
            return false;
        }
        if (!empty($minute->timeend) && $now > (int)$minute->timeend) {
            return false;
        }

        return true;
    }

    /**
     * Explain a closed window.
     *
     * @param \stdClass $minute Activity instance.
     * @param int|null $now Timestamp.
     * @return string
     */
    public static function get_window_message(\stdClass $minute, ?int $now = null): string {
        $now ??= time();

        if (!empty($minute->timestart) && $now < (int)$minute->timestart) {
            return get_string("notopenyet", "mod_minute", userdate((int)$minute->timestart));
        }
        if (!empty($minute->timeend) && $now > (int)$minute->timeend) {
            return get_string("closedat", "mod_minute", userdate((int)$minute->timeend));
        }

        return "";
    }

    /**
     * Validate presence rules for a response.
     *
     * @param \stdClass $minute Activity instance.
     * @param float|null $latitude Student latitude.
     * @param float|null $longitude Student longitude.
     * @return void
     */
    public static function validate_presence(\stdClass $minute, ?float $latitude, ?float $longitude): void {
        if (!empty($minute->requireip)) {
            $currentip = getremoteaddr();
            if (empty($minute->teacherip) || !hash_equals((string)$minute->teacherip, (string)$currentip)) {
                throw new \moodle_exception("ipnotallowed", "mod_minute");
            }
        }

        if (!empty($minute->requirelocation)) {
            if ($latitude === null || $longitude === null) {
                throw new \moodle_exception("locationrequired", "mod_minute");
            }
            if ($minute->referencelat === null || $minute->referencelon === null) {
                throw new \moodle_exception("locationnotconfigured", "mod_minute");
            }

            $distance = self::distance_meters(
                (float)$minute->referencelat,
                (float)$minute->referencelon,
                $latitude,
                $longitude
            );
            if ($distance > (int)$minute->radiusmeters) {
                throw new \moodle_exception("locationoutsideradius", "mod_minute", "", (object)[
                    "distance" => round($distance),
                    "radius" => (int)$minute->radiusmeters,
                ]);
            }
        }
    }

    /**
     * Calculate the great-circle distance between two points.
     *
     * @param float $lat1 Latitude 1.
     * @param float $lon1 Longitude 1.
     * @param float $lat2 Latitude 2.
     * @param float $lon2 Longitude 2.
     * @return float Distance in metres.
     */
    public static function distance_meters(float $lat1, float $lon1, float $lat2, float $lon2): float {
        $earthradius = 6371000.0;
        $lat1rad = deg2rad($lat1);
        $lat2rad = deg2rad($lat2);
        $dlat = deg2rad($lat2 - $lat1);
        $dlon = deg2rad($lon2 - $lon1);

        $a = sin($dlat / 2) ** 2
            + cos($lat1rad) * cos($lat2rad) * sin($dlon / 2) ** 2;
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthradius * $c;
    }
}
