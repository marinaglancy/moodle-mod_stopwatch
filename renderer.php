<?php

/**
 * This file contains a custom renderer class used by the forum module.
 *
 * @package   mod_stopwatch
 * @copyright 2004 Marina Glancy
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

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
        if ($time = stopwatch_get_completed_time($cm)) {
            $class = 'class="stopped"';
            $s = floor($time/1000) % 60;
            $m = floor($time/1000/60) % 60;
            $h = floor($time/1000/60/60);
            $timevalue = sprintf('%02d:%02d:%02d', $h, $m, $s);
        } else {
            $this->page->requires->js_init_call('M.mod_stopwatch.init', array($cm->id), true);
            $class = '';
            $timevalue = "";
        }

        $str = <<<EOD
<form id="stopwatchform" $class>
<input id="clock" value="$timevalue" class="clockdisplay" />
<input id="reset" type="button" value="Reset" class="graybutton" />
<br/>
<input id="start" type="button" value="Start" class="greenbutton" />
<input id="resume" type="button" value="Resume" class="bigstopwatchbutton greenbutton" />
<input id="pause" type="button" value="Pause" class="bigstopwatchbutton yellowbutton" />
<input id="stop" type="button" value="Stop" class="bigstopwatchbutton redbutton" />
<p class="modcompleted">You completed the module!</p>
</form>
                <br/>
EOD;
        $str .= $this->output->single_button(course_get_url($cm->course),
                get_string('back'), 'GET');
        return $str;
    }

}