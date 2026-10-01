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
 * report.php
 *
 * @package   mod_minute
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use mod_minute\access_manager;
use mod_minute\report_manager;

require_once("../../config.php");
require_once($CFG->libdir . "/tablelib.php");
require_once($CFG->libdir . "/csvlib.class.php");

$id = required_param("id", PARAM_INT);
$download = optional_param("download", "", PARAM_ALPHA);

$cm = get_coursemodule_from_id("minute", $id, 0, false, MUST_EXIST);
$course = $DB->get_record("course", ["id" => $cm->course], "*", MUST_EXIST);
$minute = $DB->get_record("minute", ["id" => $cm->instance], "*", MUST_EXIST);

require_login($course, true, $cm);
$context = context_module::instance($cm->id);
require_capability("mod/minute:viewreport", $context);

$PAGE->set_url("/mod/minute/report.php", ["id" => $cm->id]);
$PAGE->set_title(get_string("reporttitle", "mod_minute", format_string($minute->name)));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);

$groupmode = groups_get_activity_groupmode($cm);
$currentgroup = $groupmode ? groups_get_activity_group($cm, true) : 0;
$responses = report_manager::get_responses($minute, $currentgroup);

if ($download === "csv") {
    $filename = clean_filename($minute->name . "-" . get_string("responses", "mod_minute"));
    $csv = new csv_export_writer();
    $csv->set_filename($filename);

    $headers = [
        get_string("participant", "mod_minute"),
        get_string("response", "mod_minute"),
        get_string("submittedat", "mod_minute"),
    ];
    if (!empty($minute->requireip)) {
        $headers[] = get_string("ipaddress", "mod_minute");
    }
    if (!empty($minute->requirelocation)) {
        $headers[] = get_string("latitude", "mod_minute");
        $headers[] = get_string("longitude", "mod_minute");
        $headers[] = get_string("accuracy", "mod_minute");
        $headers[] = get_string("distance", "mod_minute");
    }
    $csv->add_data($headers);

    foreach ($responses as $response) {
        $row = [
            fullname($response),
            $response->response,
            userdate($response->timemodified),
        ];
        if (!empty($minute->requireip)) {
            $row[] = $response->ipaddress;
        }
        if (!empty($minute->requirelocation)) {
            $row[] = $response->latitude;
            $row[] = $response->longitude;
            $row[] = $response->accuracy;
            $distance = "";
            if ($response->latitude !== null && $response->longitude !== null
                && $minute->referencelat !== null && $minute->referencelon !== null) {
                $distance = round(access_manager::distance_meters(
                    (float)$minute->referencelat,
                    (float)$minute->referencelon,
                    (float)$response->latitude,
                    (float)$response->longitude
                ));
            }
            $row[] = $distance;
        }
        $csv->add_data($row);
    }

    $csv->download_file();
    exit;
}

$table = new flexible_table("mod-minute-report-" . $cm->id);
$columns = ["participant", "response", "submittedat"];
$headers = [
    get_string("participant", "mod_minute"),
    get_string("response", "mod_minute"),
    get_string("submittedat", "mod_minute"),
];

if (!empty($minute->requireip)) {
    $columns[] = "ipaddress";
    $headers[] = get_string("ipaddress", "mod_minute");
}
if (!empty($minute->requirelocation)) {
    $columns[] = "location";
    $headers[] = get_string("location", "mod_minute");
    $columns[] = "distance";
    $headers[] = get_string("distance", "mod_minute");
}

$table->define_columns($columns);
$table->define_headers($headers);
$table->define_baseurl(new moodle_url("/mod/minute/report.php", ["id" => $cm->id]));
$table->set_attribute("class", "generaltable generalbox");
$table->setup();

foreach ($responses as $response) {
    $row = [
        html_writer::link(new moodle_url("/user/view.php",
            ["id" => $response->userid, "course" => $course->id]), fullname($response)),
        nl2br(s($response->response)),
        userdate($response->timemodified),
    ];

    if (!empty($minute->requireip)) {
        $row[] = s($response->ipaddress);
    }

    if (!empty($minute->requirelocation)) {
        if ($response->latitude !== null && $response->longitude !== null) {
            $location = format_float((float)$response->latitude, 6) . ", " . format_float((float)$response->longitude, 6);
            if ($response->accuracy !== null) {
                $location .= " ±" . format_float((float)$response->accuracy, 0) . " m";
            }
            $row[] = $location;

            if ($minute->referencelat !== null && $minute->referencelon !== null) {
                $distance = access_manager::distance_meters(
                    (float)$minute->referencelat,
                    (float)$minute->referencelon,
                    (float)$response->latitude,
                    (float)$response->longitude
                );
                $row[] = get_string("metres", "mod_minute", round($distance));
            } else {
                $row[] = "-";
            }
        } else {
            $row[] = "-";
            $row[] = "-";
        }
    }

    $table->add_data($row);
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string("reporttitle", "mod_minute", format_string($minute->name)));

if ($groupmode) {
    groups_print_activity_menu($cm, $PAGE->url);
}

echo html_writer::start_div("d-flex justify-content-between align-items-center mb-3");
echo html_writer::div(get_string("responsecount", "mod_minute", count($responses)), "text-muted");
echo html_writer::link(
    new moodle_url("/mod/minute/report.php", ["id" => $cm->id, "download" => "csv"]),
    get_string("downloadcsv", "mod_minute"),
    ["class" => "btn btn-secondary"]
);
echo html_writer::end_div();

$table->finish_output();
echo $OUTPUT->footer();
