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

defined('MOODLE_INTERNAL') || die;

require_once("{$CFG->dirroot}/mod/minute/backup/moodle2/restore_minute_stepslib.php");

/**
 * Restore task for mod_minute.
 *
 * @package mod_minute
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restore_minute_activity_task extends restore_activity_task {
    /**
     * Define task-specific settings.
     *
     * @return void
     */
    protected function define_my_settings() {
    }

    /**
     * Define restore steps.
     *
     * @return void
     */
    protected function define_my_steps() {
        $this->add_step(new restore_minute_activity_structure_step("minute_structure", "minute.xml"));
    }

    /**
     * Define content decoding rules.
     *
     * @return restore_decode_rule[]
     */
    public static function define_decode_rules() {
        return [
            new restore_decode_rule("MINUTEVIEWBYID", "/mod/minute/view.php?id=$1", "course_module"),
            new restore_decode_rule("MINUTEINDEX", "/mod/minute/index.php?id=$1", "course"),
        ];
    }

    /**
     * Define contents that need link decoding.
     *
     * @return restore_decode_content[]
     */
    public static function define_decode_contents() {
        return [
            new restore_decode_content("minute", ["intro"], "minute"),
        ];
    }

    /**
     * Define restore log rules.
     *
     * @return restore_log_rule[]
     */
    public static function define_restore_log_rules() {
        return [];
    }
}
