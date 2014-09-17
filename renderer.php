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
        $this->page->requires->js_init_call('M.mod_stopwatch.init', array($cm->id), true);
        $str = <<<EOD
<form id="stopwatchform">
<input id="clock" value="" />
<input id="reset" type="button" value="Reset" />
<br/>
<input id="start" type="button" value="Start" />
<input id="resume" type="button" value="Resume" />
<input id="pause" type="button" value="Pause" />
<input id="stop" type="button" value="Stop" />
<p class="modcompleted">You completed the module!</p>
</form>

     
EOD;
        return $str;
    }

}