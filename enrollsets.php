<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Plugin version and other meta-data are defined here.
 *
 * @package     local_leeloolxpcareers
 * @copyright   2022 Leeloo LXP <info@leeloolxp.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');

$reqtype = optional_param('type', null, PARAM_RAW);
$reqinstance = optional_param('instance', null, PARAM_RAW);
$reqsesskey = optional_param('sesskey', null, PARAM_RAW);
$reqcourestoenrol = optional_param('courestoenrol', null, PARAM_RAW);

global $USER;
if ($USER->id) {
    foreach ($reqcourestoenrol as $course) {

        $selfinstance = $DB->get_record('enrol', array(
            'courseid' => $course,
            'enrol' => 'self',
            'status' => '0'
        ));

        $contextdata = $DB->get_record('context', array('contextlevel' => 50, 'instanceid' => $course));

        if ($contextdata->id) {

            $roleassignmentsdata = $DB->get_record('role_assignments', array('roleid' => 5, 'contextid' => $contextdata->id, 'userid' => $USER->id));

            if (!$roleassignmentsdata->id) {
                $DB->execute(
                    "INSERT INTO {role_assignments} (roleid, contextid, userid, modifierid) VALUES (?, ?, ?, ?)",
                    [5, $contextdata->id, $USER->id, 2]
                );
            }
        }

        $userenrolmentsdata = $DB->get_record('user_enrolments', array('enrolid' => $selfinstance->id, 'userid' => $USER->id));
        if (!$userenrolmentsdata->id) {
            $DB->execute(
                "INSERT INTO {user_enrolments} (status, enrolid, userid) VALUES (?, ?, ?)",
                [0, $selfinstance->id, $USER->id]
            );
        }

        $groupidarr = $DB->get_record_sql(
            "SELECT id FROM {groups} WHERE courseid = ? AND idnumber LIKE '000-%%-x-es'",
            [$course]
        );

        if ($groupidarr->id) {

            $groupsmembersdata = $DB->get_record('groups_members', array('groupid' => $groupidarr->id, 'userid' => $USER->id));
            if (!$groupsmembersdata->id) {
                $DB->execute(
                    "INSERT INTO {groups_members} (groupid, userid, timeadded, component, itemid) VALUES ( ?, ?, ?, ?, ?)",
                    [$groupidarr->id, $USER->id, time(), '', '0']
                );
            }
        }
    }
}
$urltogo = $CFG->wwwroot . '/local/leeloolxpcareers/' . $reqtype . '.php?id=' . $reqinstance;
redirect($urltogo,  get_string('successfullyenrolled', 'local_leeloolxpcareers'));
