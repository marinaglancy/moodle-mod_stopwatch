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
        if (($userrecord = mod_stopwatch_get_user_record($cm)) && $userrecord->duration) {
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
    <input type="hidden" name="cmd" value="updateduration" />
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

    public function display_grades(cm_info $cm, $stopwatch) {
        $sesskey = sesskey();
        $action = new moodle_url('/mod/stopwatch/view.php');
        $editlink = html_writer::link(new moodle_url('/course/modedit.php', array('update' => $cm->id)),
                'Edit');
        $str = '';
        if ($stopwatch->grade > 0) {
            $str .= "<p>Maximum grade for the module is <b>$stopwatch->grade</b>. $editlink</p>";
        } else if ($stopwatch->grade < 0) {
            $str .= "<p>Scale grading is not yet implemented! $editlink</p>";
        } else {
            $str .= "<p>No grading is enabled for this module. $editlink</p>";
        }
        $str = $str . <<<EOD
<form id="stopwatchgradeform" method="POST" action="$action">
    <input type="hidden" name="sesskey" value="$sesskey" />
    <input type="hidden" name="id" value="$cm->id" />
    <input type="hidden" name="cmd" value="updategrades" />
EOD;

        $table = new html_table();
        $table->head = array(
            'User',
            'Duration',
            'Completed on');
        if ($stopwatch->grade) {
            $table->head[] = 'Grade';
            $table->head[] = 'Graded on';
        }

        $records = mod_stopwatch_get_all_users($cm, $stopwatch);
        foreach ($records as $record) {
            $duration = $record->duration ? mod_stopwatch_duration_to_string($record->duration) : '';
            $durationinput = html_writer::empty_tag('input',
                    array('type' => 'text',
                        'name' => "duration[$record->userid]",
                        'value' => $duration));
            $data = (array)array(
                fullname($record),
                $durationinput,
                $record->timecreated ? userdate($record->timecreated, '%d/%m/%y <b>%H:%M</b>') : '');
            if ($stopwatch->grade > 0) {
                $gradeinput = html_writer::empty_tag('input',
                        array('type' => 'text',
                            'name' => "grade[$record->userid]",
                            'value' => strlen($record->grade) ? (float)$record->grade : ''));
                $data[] = $gradeinput;
            } else if ($stopwatch->grade < 0) {
                $data[] = 'Not implemented, sorry....';
            }
            if ($stopwatch->grade) {
                $data[] = $record->timegraded ? userdate($record->timegraded, '%d/%m/%y <b>%H:%M</b>') : '';
            }
            $table->data[] = $data;
        }

        $str .= html_writer::table($table);
        $str .= '<input id="grade" type="submit" value="Grade" />';
        $str .= '</form >';

        return $str;
    }

}