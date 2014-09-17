<?php

/**
 * Draft file ajax file manager
 *
 * @package    mod_stopwatch
 * @copyright  2014 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('AJAX_SCRIPT', true);

require_once(dirname(dirname(__DIR__)) . '/config.php');
require_once(__DIR__ . '/lib.php');
require_once($CFG->libdir . '/completionlib.php');

$cmid = required_param('id', PARAM_INT);
$timer = required_param('timer', PARAM_INT);

list($course, $cm) = get_course_and_cm_from_cmid($cmid, 'stopwatch');
require_login($course, true, $cm);
$stopwatch = $PAGE->activityrecord;
require_sesskey();

// TODO record $timer somewhere.

// Update completion state
$completion=new completion_info($course);
if($completion->is_enabled($cm) && ($stopwatch->completiontimed)) {
    $completion->update_state($cm, COMPLETION_COMPLETE);
}

echo $OUTPUT->header();
die(json_encode(array('status' => 'ok')));