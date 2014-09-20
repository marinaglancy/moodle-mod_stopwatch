<?php

/**
 * This file contains a custom renderer class used by the forum module.
 *
 * @package   mod_stopwatch
 * @copyright 2004 Marina Glancy
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/stopwatch/locallib.php');

/**
 * A custom renderer class that extends the plugin_renderer_base and
 * is used by the stopwatch module.
 *
 * @package   mod_stopwatch
 * @copyright 2004 Marina Glancy
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 **/
class mod_stopwatch_renderer extends plugin_renderer_base {

    public function display_stopwatch(cm_info $cm, $stopwatch) {
        // TODO strings!!!!
        $timecompleted = '';
        if ($userrecord = mod_stopwatch_get_user_timing($cm)) {
            $class = 'class="stopped"';
            $timevalue = mod_stopwatch_duration_to_string($userrecord->duration);
            $started = $userrecord->timecreated - $userrecord->duration;
            $completed = $userrecord->timecreated;
            // timecreated is when the record was created and it was created when user completed the module.
            $timestarted = $this->print_times($started, $completed);
            $stoplabel = 'Adjust';
        } else {
            $this->page->requires->js_init_call('M.mod_stopwatch.init', array($cm->id), true);
            $class = '';
            $timevalue = '';
            $timestarted = $this->print_times(time());
            $stoplabel = 'I\'m finished!';
        }

        $sesskey = sesskey();
        $action = new moodle_url('/mod/stopwatch/view.php');
        $str = <<<EOD
<form id="stopwatchform" method="POST" action="$action" $class>
    <input type="hidden" name="sesskey" value="$sesskey" />
    <input type="hidden" name="id" value="$cm->id" />
    <div class="clockface">
        <input id="clock" name="durationstr" value="$timevalue" class="clockdisplay" />
        <input id="reset" type="button" value="Reset" class="graybutton" />
        <div class="timestartedcompleted">$timestarted</div>
    </div>
    <br/>
    <input id="start" type="button" value="Start" class="greenbutton" />
    <input id="resume" type="button" value="Resume" class="bigstopwatchbutton greenbutton" />
    <input id="pause" type="button" value="Pause" class="bigstopwatchbutton yellowbutton" />
    <input id="stop" type="submit" value="$stoplabel" class="bigstopwatchbutton redbutton" />
EOD;
        $str .= '</form><br/>';
        $str .= $this->output->single_button(course_get_url($cm->course),
                get_string('back'), 'GET');
        return $str;
    }

    protected function print_times($started, $completed = null) {
        $rv = userdate($started, '%d/%m/%y <b>%H:%M</b>');
        if ($completed && userdate($started, '%d/%m/%y') === userdate($completed, '%d/%m/%y')) {
            $rv .= userdate($completed, ' - <b>%H:%M</b>');
        } else if ($completed) {
            $rv .= userdate($completed, ' - %d/%m/%y <b>%H:%M</b>');
        }
        return $rv;
    }

}