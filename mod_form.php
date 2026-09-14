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

require_once("{$CFG->dirroot}/course/moodleform_mod.php");

/**
 * Activity settings form.
 *
 * @package mod_minute
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_minute_mod_form extends moodleform_mod {
    /**
     * Define the form.
     *
     * @return void
     */
    public function definition() {
        global $PAGE;

        $mform = $this->_form;

        $mform->addElement("text", "name", get_string("minutename", "mod_minute"), ["size" => 64]);
        $mform->setType("name", PARAM_TEXT);
        $mform->addRule("name", null, "required", null, "client");
        $mform->addRule("name", get_string("maximumchars", "", 255), "maxlength", 255, "client");

        $this->standard_intro_elements();

        $mform->addElement("html", html_writer::tag("h3", get_string("contentheader", "mod_minute")));
        $mform->addElement("textarea", "prompt", get_string("prompt", "mod_minute"), ["rows" => 4, "cols" => 80]);
        $mform->setType("prompt", PARAM_TEXT);
        $mform->setDefault("prompt", get_string("defaultprompt", "mod_minute"));
        $mform->addRule("prompt", null, "required", null, "client");

        $mform->addElement("text", "maxchars", get_string("maxchars", "mod_minute"), ["size" => 8]);
        $mform->setType("maxchars", PARAM_INT);
        $mform->setDefault("maxchars", 500);
        $mform->addRule("maxchars", null, "required", null, "client");
        $mform->addHelpButton("maxchars", "maxchars", "mod_minute");

        $mform->addElement("date_time_selector", "timestart", get_string("timestart", "mod_minute"), ["optional" => true]);
        $mform->addElement("date_time_selector", "timeend", get_string("timeend", "mod_minute"), ["optional" => true]);

        $mform->addElement("header", "presenceheader", get_string("presenceheader", "mod_minute"));

        $mform->addElement("advcheckbox", "requireip", get_string("requireip", "mod_minute"));
        $mform->addHelpButton("requireip", "requireip", "mod_minute");

        $mform->addElement("text", "teacherip", get_string("teacherip", "mod_minute"), ["size" => 48]);
        $mform->setType("teacherip", PARAM_RAW_TRIMMED);
        $mform->setDefault("teacherip", getremoteaddr());
        $mform->hideIf("teacherip", "requireip", "notchecked");
        $mform->addHelpButton("teacherip", "teacherip", "mod_minute");

        $mform->addElement("advcheckbox", "requirelocation", get_string("requirelocation", "mod_minute"));
        $mform->addHelpButton("requirelocation", "requirelocation", "mod_minute");

        $mform->addElement("text", "referencelat", get_string("referencelat", "mod_minute"),
            ["size" => 20, "readonly" => "readonly"]);
        $mform->setType("referencelat", PARAM_RAW_TRIMMED);
        $mform->hideIf("referencelat", "requirelocation", "notchecked");

        $mform->addElement("text", "referencelon", get_string("referencelon", "mod_minute"),
            ["size" => 20, "readonly" => "readonly"]);
        $mform->setType("referencelon", PARAM_RAW_TRIMMED);
        $mform->hideIf("referencelon", "requirelocation", "notchecked");

        $mform->addElement("button", "capturelocation", get_string("captureteacherlocation", "mod_minute"),
            ["id" => "id_captureteacherlocation"]);
        $mform->hideIf("capturelocation", "requirelocation", "notchecked");

        $mform->addElement("static", "locationstatus", "", html_writer::span("", "text-muted", ["id" => "minute-location-status"]));
        $mform->hideIf("locationstatus", "requirelocation", "notchecked");

        $mform->addElement("text", "radiusmeters", get_string("radiusmeters", "mod_minute"), ["size" => 8]);
        $mform->setType("radiusmeters", PARAM_INT);
        $mform->setDefault("radiusmeters", 100);
        $mform->hideIf("radiusmeters", "requirelocation", "notchecked");
        $mform->addHelpButton("radiusmeters", "radiusmeters", "mod_minute");

        $this->standard_coursemodule_elements();
        $this->add_action_buttons();

        $PAGE->requires->js_call_amd("mod_minute/location", "initSettings", [[
            "button" => "#id_captureteacherlocation",
            "latitude" => "#id_referencelat",
            "longitude" => "#id_referencelon",
            "status" => "#minute-location-status",
            "strings" => [
                "requesting" => get_string("locationrequesting", "mod_minute"),
                "captured" => get_string("locationcaptured", "mod_minute"),
                "error" => get_string("locationerror", "mod_minute"),
            ],
        ]]);
    }

    /**
     * Validate settings.
     *
     * @param array $data Submitted data.
     * @param array $files Submitted files.
     * @return array
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        if (!empty($data["timestart"]) && !empty($data["timeend"]) && $data["timeend"] <= $data["timestart"]) {
            $errors["timeend"] = get_string("errorendbeforestart", "mod_minute");
        }

        $maxchars = (int)($data["maxchars"] ?? 0);
        if ($maxchars < 20 || $maxchars > 2000) {
            $errors["maxchars"] = get_string("errormaxchars", "mod_minute");
        }

        if (!empty($data["requireip"])) {
            $ip = trim((string)($data["teacherip"] ?? ""));
            if ($ip !== "" && filter_var($ip, FILTER_VALIDATE_IP) === false) {
                $errors["teacherip"] = get_string("errorinvalidip", "mod_minute");
            }
        }

        if (!empty($data["requirelocation"])) {
            $lat = trim((string)($data["referencelat"] ?? ""));
            $lon = trim((string)($data["referencelon"] ?? ""));
            if ($lat === "" || $lon === "") {
                $errors["referencelat"] = get_string("errorreferencelocation", "mod_minute");
            } else if (!is_numeric($lat) || (float)$lat < -90 || (float)$lat > 90) {
                $errors["referencelat"] = get_string("errorlatitude", "mod_minute");
            } else if (!is_numeric($lon) || (float)$lon < -180 || (float)$lon > 180) {
                $errors["referencelon"] = get_string("errorlongitude", "mod_minute");
            }

            $radius = (int)($data["radiusmeters"] ?? 0);
            if ($radius < 10 || $radius > 5000) {
                $errors["radiusmeters"] = get_string("errorradius", "mod_minute");
            }
        }

        return $errors;
    }
}
