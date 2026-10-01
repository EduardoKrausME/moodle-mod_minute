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
 * view.php
 *
 * @package   mod_minute
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core\output\notification;
use mod_minute\access_manager;
use mod_minute\event\course_module_viewed;
use mod_minute\presence_manager;
use mod_minute\submission_manager;

require_once("../../config.php");

$id = required_param("id", PARAM_INT);
$action = optional_param("action", "", PARAM_ALPHA);

$cm = get_coursemodule_from_id("minute", $id, 0, false, MUST_EXIST);
$course = $DB->get_record("course", ["id" => $cm->course], "*", MUST_EXIST);
$minute = $DB->get_record("minute", ["id" => $cm->instance], "*", MUST_EXIST);

require_login($course, true, $cm);
$context = context_module::instance($cm->id);
require_capability("mod/minute:view", $context);

$PAGE->set_url("/mod/minute/view.php", ["id" => $cm->id]);
$PAGE->set_title(format_string($minute->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);

if ($action !== "") {
    require_sesskey();

    if ($action === "submit") {
        $response = required_param("response", PARAM_RAW);
        $latraw = optional_param("latitude", "", PARAM_RAW_TRIMMED);
        $lonraw = optional_param("longitude", "", PARAM_RAW_TRIMMED);
        $accuracyraw = optional_param("accuracy", "", PARAM_RAW_TRIMMED);

        if ($latraw !== "" && !is_numeric($latraw)) {
            throw new moodle_exception("errorlatitude", "mod_minute");
        }
        if ($lonraw !== "" && !is_numeric($lonraw)) {
            throw new moodle_exception("errorlongitude", "mod_minute");
        }
        if ($accuracyraw !== "" && !is_numeric($accuracyraw)) {
            throw new moodle_exception("erroraccuracy", "mod_minute");
        }

        $latitude = $latraw === "" ? null : (float)$latraw;
        $longitude = $lonraw === "" ? null : (float)$lonraw;
        $accuracy = $accuracyraw === "" ? null : (float)$accuracyraw;

        submission_manager::save(
            $minute,
            $context,
            $response,
            $latitude,
            $longitude,
            $accuracy
        );

        redirect(
            new moodle_url("/mod/minute/view.php", ["id" => $cm->id]),
            get_string("responsesaved", "mod_minute"),
            null,
            notification::NOTIFY_SUCCESS
        );
    }

    if ($action === "captureip") {
        presence_manager::capture_ip($minute, $context);
        redirect(
            new moodle_url("/mod/minute/view.php", ["id" => $cm->id]),
            get_string("ipcaptured", "mod_minute"),
            null,
            notification::NOTIFY_SUCCESS
        );
    }

    if ($action === "capturelocation") {
        $latitude = required_param("latitude", PARAM_FLOAT);
        $longitude = required_param("longitude", PARAM_FLOAT);
        presence_manager::capture_location($minute, $context, $latitude, $longitude);
        redirect(
            new moodle_url("/mod/minute/view.php", ["id" => $cm->id]),
            get_string("teacherlocationcaptured", "mod_minute"),
            null,
            notification::NOTIFY_SUCCESS
        );
    }
}

$event = course_module_viewed::create([
    "objectid" => $minute->id,
    "context" => $context,
]);
$event->add_record_snapshot("course", $course);
$event->add_record_snapshot("minute", $minute);
$event->trigger();

$completion = new completion_info($course);
$completion->set_module_viewed($cm);

$currentresponse = null;
if (has_capability("mod/minute:submit", $context)) {
    $currentresponse = submission_manager::get_for_user($minute->id, $USER->id);
}

$isopen = access_manager::is_open($minute);
$canreport = has_capability("mod/minute:viewreport", $context);
$cansubmit = has_capability("mod/minute:submit", $context);

$windowparts = [];
if (!empty($minute->timestart)) {
    $windowparts[] = get_string("opens", "mod_minute", userdate($minute->timestart));
}
if (!empty($minute->timeend)) {
    $windowparts[] = get_string("closes", "mod_minute", userdate($minute->timeend));
}

$templatecontext = [
    "name" => format_string($minute->name),
    "intro" => format_module_intro("minute", $minute, $cm->id),
    "hasintro" => trim((string)$minute->intro) !== "",
    "prompt" => format_text($minute->prompt, FORMAT_PLAIN),
    "maxchars" => (int)$minute->maxchars,
    "characterlimittext" => get_string("characterlimit", "mod_minute", (int)$minute->maxchars),
    "window" => implode(" · ", $windowparts),
    "haswindow" => !empty($windowparts),
    "isopen" => $isopen,
    "closedmessage" => $isopen ? "" : access_manager::get_window_message($minute),
    "cansubmit" => $cansubmit,
    "canreport" => $canreport,
    "response" => $currentresponse ? $currentresponse->response : "",
    "hassubmission" => (bool)$currentresponse,
    "submittedat" => $currentresponse ? userdate($currentresponse->timemodified) : "",
    "requirelocation" => !empty($minute->requirelocation),
    "requireip" => !empty($minute->requireip),
    "teacherip" => (string)$minute->teacherip,
    "currentteacheriptext" => get_string("currentteacherip", "mod_minute", (string)$minute->teacherip),
    "hasteacherip" => !empty($minute->teacherip),
    "hasreferencelocation" => $minute->referencelat !== null && $minute->referencelon !== null,
    "referencelocation" => $minute->referencelat !== null && $minute->referencelon !== null
        ? format_float((float)$minute->referencelat, 6) . ", " . format_float((float)$minute->referencelon, 6)
        : "",
    "referencelocationtext" => $minute->referencelat !== null && $minute->referencelon !== null
        ? get_string("currentreferencelocation", "mod_minute",
            format_float((float)$minute->referencelat, 6) . ", " . format_float((float)$minute->referencelon, 6))
        : "",
    "radiusmeters" => (int)$minute->radiusmeters,
    "radiustext" => get_string("radiusdisplay", "mod_minute", (int)$minute->radiusmeters),
    "lastsavedtext" => $currentresponse ? get_string("lastsaved", "mod_minute", userdate($currentresponse->timemodified)) : "",
    "reporturl" => (new moodle_url("/mod/minute/report.php", ["id" => $cm->id]))->out(false),
    "sesskey" => sesskey(),
    "actionurl" => (new moodle_url("/mod/minute/view.php", ["id" => $cm->id]))->out(false),
    "id" => $cm->id,
];

if ($cansubmit && $isopen && !empty($minute->requirelocation)) {
    $PAGE->requires->js_call_amd("mod_minute/location", "initSubmission", [[
        "button" => "#minute-capture-location",
        "latitude" => "#minute-latitude",
        "longitude" => "#minute-longitude",
        "accuracy" => "#minute-accuracy",
        "status" => "#minute-location-status",
        "submit" => "#minute-submit",
        "strings" => [
            "requesting" => get_string("locationrequesting", "mod_minute"),
            "captured" => get_string("locationcaptured", "mod_minute"),
            "error" => get_string("locationerror", "mod_minute"),
        ],
    ]]);
}

if ($canreport && !empty($minute->requirelocation)) {
    $PAGE->requires->js_call_amd("mod_minute/location", "initTeacherCapture", [[
        "button" => "#minute-teacher-location",
        "form" => "#minute-teacher-location-form",
        "latitude" => "#minute-teacher-latitude",
        "longitude" => "#minute-teacher-longitude",
        "status" => "#minute-teacher-location-status",
        "strings" => [
            "requesting" => get_string("locationrequesting", "mod_minute"),
            "captured" => get_string("locationcapturedsaving", "mod_minute"),
            "error" => get_string("locationerror", "mod_minute"),
        ],
    ]]);
}

echo $OUTPUT->header();
echo $OUTPUT->render_from_template("mod_minute/view", $templatecontext);
echo $OUTPUT->footer();
