<?php

/**
 * Prints a particular instance of stopwatch
 *
 * You can have a rather longer description of the file as well,
 * if you like, and it can span multiple lines.
 *
 * @package    mod_stopwatch
 * @copyright  2014 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/// (Replace stopwatch with the name of your module and remove this line)

require_once(dirname(dirname(__DIR__)) . '/config.php');
require_once($CFG->dirroot . '/mod/stopwatch/lib.php');
require_once($CFG->dirroot . '/mod/stopwatch/locallib.php');
require_once($CFG->libdir . '/completionlib.php');

$cmid = optional_param('id', 0, PARAM_INT); // course_module ID, or
$stopwatchid = optional_param('s', 0, PARAM_INT);  // stopwatch instance ID - it should be named as the first character of the module
$durationstr = optional_param('durationstr', null, PARAM_NOTAGS);

if ($cmid) {
    list($course, $cm) = get_course_and_cm_from_cmid($cmid, 'stopwatch');
} elseif ($stopwatchid) {
    list($course, $cm) = get_course_and_cm_from_instance($stopwatchid, 'stopwatch');
} else {
    print_error('You must specify a course_module ID or an instance ID');
}

require_login($course, true, $cm);
$stopwatch = $PAGE->activityrecord;

if ($durationstr && confirm_sesskey()) {
    mod_stopwatch_update_timer($cm, $stopwatch,
            mod_stopwatch_string_to_duration($durationstr));
    redirect($cm->url);
}

\mod_stopwatch\event\course_module_viewed::create_from_cm($cm, $course, $stopwatch)->trigger();

// Mark viewed if required
$completion = new completion_info($course);
$completion->set_module_viewed($cm);

/// Print the page header

$PAGE->set_url($cm->url);
$PAGE->set_title($cm->get_formatted_name());
$PAGE->set_heading($course->fullname);

// other things you may want to set - remove if not needed
//$PAGE->set_cacheable(false);
//$PAGE->set_focuscontrol('some-html-id');
//$PAGE->add_body_class('stopwatch-'.$somevar);

// Output starts here
$output = $PAGE->get_renderer('mod_stopwatch');
echo $output->header();

if ($stopwatch->intro) { // Conditions to show the intro can change to look for own settings or whatever
    echo $output->box(format_module_intro('stopwatch', $stopwatch, $cm->id), 'generalbox mod_introbox', 'stopwatchintro');
}

// Replace the following lines with you own code
echo $output->heading($cm->get_formatted_name());

echo $output->display_stopwatch($cm, $stopwatch);

// Finish the page
echo $output->footer();
