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
 * minute.php
 *
 * @package   mod_minute
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

$string['accuracy'] = 'Accuracy (m)';
$string['captureteacherlocation'] = 'Capture my current location';
$string['characterlimit'] = 'Limit: {$a} characters';
$string['closed'] = 'Closed';
$string['closedat'] = 'This activity closed on {$a}.';
$string['closes'] = 'Closes {$a}';
$string['contentheader'] = 'Prompt and response';
$string['currentreferencelocation'] = 'Reference location: {$a}';
$string['currentteacherip'] = 'Reference IP: {$a}';
$string['defaultprompt'] = 'In approximately one minute, write the main concept you learned.';
$string['distance'] = 'Distance from reference';
$string['downloadcsv'] = 'Download CSV';
$string['erroraccuracy'] = 'Location accuracy cannot be negative.';
$string['errorendbeforestart'] = 'The close time must be later than the open time.';
$string['errorinvalidip'] = 'Enter a valid IPv4 or IPv6 address.';
$string['errorlatitude'] = 'Latitude must be between -90 and 90.';
$string['errorlongitude'] = 'Longitude must be between -180 and 180.';
$string['errormaxchars'] = 'Enter a value between 20 and 2000 characters.';
$string['errorradius'] = 'Enter a radius between 10 and 5000 metres.';
$string['errorreferencelocation'] = 'Capture the teacher location before saving when geolocation is required.';
$string['eventcoursemoduleviewed'] = 'One-minute paper viewed';
$string['eventresponsesubmitted'] = 'One-minute paper response submitted';
$string['eventresponseupdated'] = 'One-minute paper response updated';
$string['ipaddress'] = 'IP address';
$string['ipcaptured'] = 'Teacher reference IP updated.';
$string['ipnotallowed'] = 'Your current IP address does not match the teacher reference IP.';
$string['iprestriction'] = 'IP restriction';
$string['lastsaved'] = 'Last saved: {$a}';
$string['latitude'] = 'Latitude';
$string['location'] = 'Location';
$string['locationcaptured'] = 'Location captured.';
$string['locationcapturedsaving'] = 'Location captured. Saving…';
$string['locationerror'] = 'Could not obtain your location. Check browser permission and HTTPS.';
$string['locationnotconfigured'] = 'The teacher reference location has not been configured yet.';
$string['locationnotconfiguredteacher'] = 'No teacher reference location has been configured yet.';
$string['locationoutsideradius'] = 'You are approximately {$a->distance} m from the reference point. The allowed radius is {$a->radius} m.';
$string['locationrequesting'] = 'Requesting location…';
$string['locationrequired'] = 'Location is required to submit this activity.';
$string['locationrestriction'] = 'Location restriction';
$string['longitude'] = 'Longitude';
$string['maxchars'] = 'Maximum characters';
$string['maxchars_help'] = 'Maximum length of the student\'s response. Allowed range: 20 to 2000 characters.';
$string['metres'] = '{$a} m';
$string['minute:addinstance'] = 'Add a new One-minute paper activity';
$string['minute:submit'] = 'Submit a One-minute paper response';
$string['minute:view'] = 'View a One-minute paper activity';
$string['minute:viewreport'] = 'View One-minute paper responses';
$string['minutename'] = 'Activity name';
$string['modulename'] = 'One-minute paper';
$string['modulenameplural'] = 'One-minute papers';
$string['noinstances'] = 'There are no One-minute paper activities in this course.';
$string['notopenyet'] = 'This activity opens on {$a}.';
$string['open'] = 'Open';
$string['opens'] = 'Opens {$a}';
$string['participant'] = 'Participant';
$string['pluginadministration'] = 'One-minute paper administration';
$string['pluginname'] = 'One-minute paper';
$string['presenceheader'] = 'Presence validation';
$string['privacy:metadata:minute_responses'] = 'Stores each participant\'s response and, when enabled by the teacher, presence validation data.';
$string['privacy:metadata:minute_responses:accuracy'] = 'The browser-provided geolocation accuracy in metres.';
$string['privacy:metadata:minute_responses:ipaddress'] = 'The IP address detected when the response was saved, when IP validation is enabled.';
$string['privacy:metadata:minute_responses:latitude'] = 'The browser-provided latitude when geolocation is enabled.';
$string['privacy:metadata:minute_responses:longitude'] = 'The browser-provided longitude when geolocation is enabled.';
$string['privacy:metadata:minute_responses:response'] = 'The text submitted by the user.';
$string['privacy:metadata:minute_responses:timecreated'] = 'When the response was first created.';
$string['privacy:metadata:minute_responses:timemodified'] = 'When the response was last updated.';
$string['privacy:metadata:minute_responses:userid'] = 'The user who submitted the response.';
$string['prompt'] = 'Prompt';
$string['question'] = 'One-minute paper';
$string['radiusdisplay'] = 'Radius: {$a} m';
$string['radiusmeters'] = 'Allowed radius (metres)';
$string['radiusmeters_help'] = 'Maximum distance between the student\'s browser location and the teacher reference location. Allowed range: 10 to 5000 metres.';
$string['referencelat'] = 'Teacher latitude';
$string['referencelon'] = 'Teacher longitude';
$string['reporttitle'] = 'Responses: {$a}';
$string['requireip'] = 'Require the same IP address as the teacher';
$string['requireip_help'] = 'Students can submit only when Moodle detects exactly the same public/client IP address configured for the teacher. This works well when teacher and students use the same network, but can fail with mobile networks, VPNs, proxies or incorrectly configured reverse proxies.';
$string['requirelocation'] = 'Require geolocation near the teacher';
$string['requirelocation_help'] = 'Students must grant browser geolocation permission and be within the configured radius of the teacher reference position. Browser geolocation normally requires HTTPS.';
$string['response'] = 'Response';
$string['responsecount'] = '{$a} response(s)';
$string['responseempty'] = 'Write a response before submitting.';
$string['responses'] = 'responses';
$string['responsesaved'] = 'Your response was saved.';
$string['responsetoolong'] = 'The response exceeds the maximum length of {$a} characters.';
$string['sharelocation'] = 'Share my location';
$string['status'] = 'Status';
$string['studentipnotice'] = 'Your response is accepted only if Moodle detects the same IP address as the teacher reference.';
$string['submissionclosed'] = 'This activity is not currently accepting responses.';
$string['submitresponse'] = 'Submit response';
$string['submittedat'] = 'Last submitted';
$string['teachercontrols'] = 'Teacher controls';
$string['teacherip'] = 'Teacher reference IP';
$string['teacherip_help'] = 'The IP address students must match. The activity initially uses the IP detected when the teacher saves it, and the reference can be refreshed from the activity page.';
$string['teacherlocationcaptured'] = 'Teacher reference location updated.';
$string['teachernotconfigured'] = 'No reference IP has been configured yet.';
$string['timeend'] = 'Close at';
$string['timestart'] = 'Open from';
$string['updateresponse'] = 'Update response';
$string['usecurrentip'] = 'Use my current IP';
$string['usecurrentlocation'] = 'Use my current location';
$string['viewreport'] = 'View responses';
$string['yourresponse'] = 'Your response';
