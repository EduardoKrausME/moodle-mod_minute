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

namespace mod_minute\privacy;

use context;
use context_module;
use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\approved_userlist;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\core_userlist_provider;
use core_privacy\local\request\userlist;
use core_privacy\local\request\transform;
use core_privacy\local\request\writer;

/**
 * Privacy provider for One-minute paper.
 *
 * @package mod_minute
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
    \core_privacy\local\metadata\provider,
    core_userlist_provider,
    \core_privacy\local\request\plugin\provider {

    /**
     * Describe stored personal data.
     *
     * @param collection $collection Metadata collection.
     * @return collection
     */
    public static function get_metadata(collection $collection): collection {
        $collection->add_database_table("minute_responses", [
            "userid" => "privacy:metadata:minute_responses:userid",
            "response" => "privacy:metadata:minute_responses:response",
            "ipaddress" => "privacy:metadata:minute_responses:ipaddress",
            "latitude" => "privacy:metadata:minute_responses:latitude",
            "longitude" => "privacy:metadata:minute_responses:longitude",
            "accuracy" => "privacy:metadata:minute_responses:accuracy",
            "timecreated" => "privacy:metadata:minute_responses:timecreated",
            "timemodified" => "privacy:metadata:minute_responses:timemodified",
        ], "privacy:metadata:minute_responses");

        return $collection;
    }

    /**
     * Get module contexts containing data for a user.
     *
     * @param int $userid User id.
     * @return contextlist
     */
    public static function get_contexts_for_userid(int $userid): contextlist {
        $contextlist = new contextlist();
        $sql = "SELECT ctx.id
                  FROM {context} ctx
                  JOIN {course_modules} cm ON cm.id = ctx.instanceid
                  JOIN {modules} m ON m.id = cm.module AND m.name = :modname
                  JOIN {minute} mn ON mn.id = cm.instance
                  JOIN {minute_responses} r ON r.minuteid = mn.id
                 WHERE ctx.contextlevel = :contextlevel
                   AND r.userid = :userid";
        $contextlist->add_from_sql($sql, [
            "modname" => "minute",
            "contextlevel" => CONTEXT_MODULE,
            "userid" => $userid,
        ]);

        return $contextlist;
    }

    /**
     * Export user data.
     *
     * @param approved_contextlist $contextlist Approved contexts.
     * @return void
     */
    public static function export_user_data(approved_contextlist $contextlist): void {
        global $DB;

        foreach ($contextlist->get_contexts() as $context) {
            if (!$context instanceof context_module) {
                continue;
            }

            $cm = get_coursemodule_from_id("minute", $context->instanceid);
            if (!$cm) {
                continue;
            }

            $response = $DB->get_record("minute_responses", [
                "minuteid" => $cm->instance,
                "userid" => $contextlist->get_user()->id,
            ]);
            if (!$response) {
                continue;
            }

            $data = (object)[
                "response" => $response->response,
                "ipaddress" => $response->ipaddress,
                "latitude" => $response->latitude,
                "longitude" => $response->longitude,
                "accuracy" => $response->accuracy,
                "timecreated" => transform::datetime($response->timecreated),
                "timemodified" => transform::datetime($response->timemodified),
            ];
            writer::with_context($context)->export_data([], $data);
        }
    }

    /**
     * Delete all user data in one module context.
     *
     * @param context $context Context.
     * @return void
     */
    public static function delete_data_for_all_users_in_context(context $context): void {
        global $DB;

        if (!$context instanceof context_module) {
            return;
        }
        $cm = get_coursemodule_from_id("minute", $context->instanceid);
        if ($cm) {
            $DB->delete_records("minute_responses", ["minuteid" => $cm->instance]);
        }
    }

    /**
     * Delete data for one user from approved contexts.
     *
     * @param approved_contextlist $contextlist Approved contexts.
     * @return void
     */
    public static function delete_data_for_user(approved_contextlist $contextlist): void {
        global $DB;

        foreach ($contextlist->get_contexts() as $context) {
            if (!$context instanceof context_module) {
                continue;
            }
            $cm = get_coursemodule_from_id("minute", $context->instanceid);
            if ($cm) {
                $DB->delete_records("minute_responses", [
                    "minuteid" => $cm->instance,
                    "userid" => $contextlist->get_user()->id,
                ]);
            }
        }
    }

    /**
     * Add users with data in the supplied context.
     *
     * @param userlist $userlist User list.
     * @return void
     */
    public static function get_users_in_context(userlist $userlist): void {
        $context = $userlist->get_context();
        if (!$context instanceof context_module) {
            return;
        }

        $sql = "SELECT r.userid
                  FROM {minute_responses} r
                  JOIN {course_modules} cm ON cm.instance = r.minuteid
                  JOIN {modules} m ON m.id = cm.module AND m.name = :modname
                 WHERE cm.id = :cmid";
        $userlist->add_from_sql("userid", $sql, [
            "modname" => "minute",
            "cmid" => $context->instanceid,
        ]);
    }

    /**
     * Delete data for approved users in one context.
     *
     * @param approved_userlist $userlist Approved users.
     * @return void
     */
    public static function delete_data_for_users(approved_userlist $userlist): void {
        global $DB;

        $context = $userlist->get_context();
        if (!$context instanceof context_module) {
            return;
        }
        $cm = get_coursemodule_from_id("minute", $context->instanceid);
        if (!$cm) {
            return;
        }

        $userids = $userlist->get_userids();
        if ($userids) {
            [$insql, $params] = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED);
            $params["minuteid"] = $cm->instance;
            $DB->delete_records_select("minute_responses", "minuteid = :minuteid AND userid {$insql}", $params);
        }
    }
}
