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
 * lib.php
 *
 * @package   mod_minute
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Declare supported Moodle features.
 *
 * @param string $feature Feature constant.
 * @return mixed
 */
function minute_supports($feature) {
    return match ($feature) {
        FEATURE_MOD_ARCHETYPE => MOD_ARCHETYPE_OTHER,
        FEATURE_MOD_INTRO => true,
        FEATURE_SHOW_DESCRIPTION => true,
        FEATURE_BACKUP_MOODLE2 => true,
        FEATURE_COMPLETION_TRACKS_VIEWS => true,
        FEATURE_GROUPS => true,
        FEATURE_GROUPINGS => true,
        FEATURE_MOD_PURPOSE => MOD_PURPOSE_ASSESSMENT,
        default => null,
    };
}

/**
 * Add a minute activity instance.
 *
 * @param stdClass $data Form data.
 * @param mod_minute_mod_form|null $mform Form instance.
 * @return int
 */
function minute_add_instance($data, $mform = null) {
    return \mod_minute\instance_manager::add($data);
}

/**
 * Update a minute activity instance.
 *
 * @param stdClass $data Form data.
 * @param mod_minute_mod_form|null $mform Form instance.
 * @return bool
 */
function minute_update_instance($data, $mform = null) {
    return \mod_minute\instance_manager::update($data);
}

/**
 * Delete a minute activity instance.
 *
 * @param int $id Instance id.
 * @return bool
 */
function minute_delete_instance($id) {
    return \mod_minute\instance_manager::delete($id);
}
