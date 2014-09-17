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
 * Does something really useful with the passed things
 *
 * @param array $things
 * @return object
 */
//function stopwatch_do_something_useful(array $things) {
//    return new stdClass();
//}

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