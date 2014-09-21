<?php

/**
 * Internal library of functions for module stopwatch
 *
 * All the stopwatch specific functions, needed to implement the module
 * logic, should go here. Never include this file from your lib.php!
 *
 * @package    mod_stopwatch
 * @copyright  2014 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Returns time (in milliseconds) a user has completed the module with
 *
 * @param stdClass|cm_info $cm Course-module
 * @param int $userid User ID
 * @return int|false
 */
function mod_stopwatch_get_user_record($cm, $userid = null) {
    global $DB, $USER;
    if ($userid === null) {
        $userid = $USER->id;
    }
    return $DB->get_record('stopwatch_user',
            array('courseid' => $cm->course,
                'stopwatchid' => $cm->instance,
                'userid' => $userid));
}

function mod_stopwatch_string_to_duration($durationstr) {
    if (empty($durationstr)) {
        return 0;
    }
    $els = array_reverse(preg_split('/:/', trim($durationstr), 3));
    $els[] = 0;
    $els[] = 0;
    return (int)$els[0] + ((int)$els[1]) * 60 + ((int)$els[2]) * 3600;
}

function mod_stopwatch_duration_to_string($duration) {
    $s = $duration % 60;
    $m = floor($duration/60) % 60;
    $h = floor($duration/60/60);
    return sprintf('%02d:%02d:%02d', $h, $m, $s);
}

/**
 *
 * @param cm_info $cm
 * @param stdClass $stopwatch
 * @param int $duration
 */
function mod_stopwatch_update_timer(cm_info $cm, $stopwatch, $duration) {
    global $USER, $DB, $CFG;
    require_once($CFG->libdir . '/completionlib.php');

    $record = $DB->get_record('stopwatch_user', array(
        'stopwatchid' => $cm->instance,
        'courseid' => $cm->course,
        'userid' => $USER->id));
    if ($record) {
        $data = array(
            'id' => $record->id,
            'timemodified' => time(),
            'duration' => $duration
        );
        $DB->update_record('stopwatch_user', $data);
    } else {
        $data = array(
            'courseid' => $cm->course,
            'stopwatchid' => $cm->instance,
            'userid' => $USER->id,
            'timecreated' => time(),
            'timemodified' => time(),
            'duration' => $duration
        );
        $DB->insert_record('stopwatch_user', $data);
    }

    // Update completion state
    $completion = new completion_info($cm->get_course());
    if($completion->is_enabled($cm) && ($stopwatch->completiontimed)) {
        $completion->update_state($cm, COMPLETION_COMPLETE);
    }
}

function mod_stopwatch_update_grades($cm, $stopwatch, $durationarray, $gradearray) {
    global $DB, $CFG;
    require_once($CFG->libdir.'/gradelib.php');
    $currentgrades = mod_stopwatch_get_all_users($cm, $stopwatch);
    $newgrades = array();
    foreach ($currentgrades as $userid => $record) {
        if ($record->id) {
            $params = array('id' => $record->id);
        } else {
            $params = array('userid' => $userid, 'courseid' => $cm->course, 'stopwatchid' => $stopwatch->id);
        }
        $now = time();
        $updateobj = array();
        if (array_key_exists($userid, $durationarray)) {
            if (empty($durationarray[$userid])) {
                if (!empty($record->duration)) {
                    $updateobj['duration'] = 0;
                }
            } else {
                $duration = mod_stopwatch_string_to_duration($durationarray[$userid]);
                if ($record->duration != $duration) {
                    $updateobj['duration'] = $duration;
                }
            }
        }
        if ($stopwatch->grade && array_key_exists($userid, $gradearray)) {
            $grade = empty($gradearray[$userid]) ? null : (float)$gradearray[$userid];
            $existinggrade = !strlen($record->grade) ? null : (float)$record->grade;
            if ($existinggrade !== $grade) {
                $updateobj['grade'] = $grade;
                $updateobj['timegraded'] = $now;
                $newgrades[$userid] = array('userid' => $userid, 'rawgrade' => $grade);
            }
        }
        if (empty($updateobj)) {
            continue;
        }
        $updateobj += $params;
        $updateobj['timemodified'] = $now;
        if (!empty($updateobj['id'])) {
            $DB->update_record('stopwatch_user', $updateobj);
        } else {
            $updateobj['timecreated'] = $now;
            $DB->insert_record('stopwatch_user', $updateobj);
        }
    }
    if ($newgrades) {
        // Attempt to update the grade item if relevant
        $grademodule = fullclone($stopwatch);
        $grademodule->cmidnumber = $cm->idnumber;
        $grademodule->modname = $cm->modname;
        grade_update_mod_grades($grademodule);
    }
}

function mod_stopwatch_get_all_users(cm_info $cm, $stopwatch) {
    global $DB;
    $context = context_module::instance($cm->id);
    list($sql, $params) = get_enrolled_sql($context, 'mod/stopwatch:submit');

    $extrauserfields = get_extra_user_fields($context);
    $fields = user_picture::fields('u', $extrauserfields, 'userid');
    $sql = "SELECT $fields, s.id, s.duration, s.timecreated, s.grade, s.timegraded
        FROM ($sql) e
        JOIN {user} u ON e.id = u.id
        LEFT JOIN {stopwatch_user} s ON e.id = s.userid AND
            s.courseid = :courseid AND s.stopwatchid = :stopwatchid";
    $params['courseid'] = $cm->course;
    $params['stopwatchid'] = $stopwatch->id;
    return $DB->get_records_sql($sql, $params);
}
