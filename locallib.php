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
function mod_stopwatch_get_user_timing($cm, $userid = null) {
    global $DB, $USER;
    if ($userid === null) {
        $userid = $USER->id;
    }
    return $DB->get_record('stopwatch_timing',
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

    $record = $DB->get_record('stopwatch_timing', array(
        'stopwatchid' => $cm->instance,
        'userid' => $USER->id));
    if ($record) {
        $data = array(
            'id' => $record->id,
            'timemodified' => time(),
            'duration' => $duration
        );
        $DB->update_record('stopwatch_timing', $data);
    } else {
        $data = array(
            'courseid' => $cm->course,
            'stopwatchid' => $cm->instance,
            'userid' => $USER->id,
            'timecreated' => time(),
            'timemodified' => time(),
            'duration' => $duration
        );
        $DB->insert_record('stopwatch_timing', $data);
    }

    // Update completion state
    $completion = new completion_info($cm->get_course());
    if($completion->is_enabled($cm) && ($stopwatch->completiontimed)) {
        $completion->update_state($cm, COMPLETION_COMPLETE);
    }
}