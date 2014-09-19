<?php

/**
 * @package   mod_stopwatch
 * @category  backup
 * @copyright 2014 Marina Glancy
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/stopwatch/backup/moodle2/restore_stopwatch_stepslib.php'); // Because it exists (must)

/**
 * stopwatch restore task that provides all the settings and steps to perform one
 * complete restore of the activity
 *
 * @package   mod_stopwatch
 * @category  backup
 * @copyright 2014 Marina Glancy
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restore_stopwatch_activity_task extends restore_activity_task {

    /**
     * Define (add) particular settings this activity can have
     */
    protected function define_my_settings() {
        // No particular settings for this activity
    }

    /**
     * Define (add) particular steps this activity can have
     */
    protected function define_my_steps() {
        // label only has one structure step
        $this->add_step(new restore_stopwatch_activity_structure_step('stopwatch_structure', 'stopwatch.xml'));
    }

    /**
     * Define the contents in the activity that must be
     * processed by the link decoder
     */
    static public function define_decode_contents() {
        $contents = array();

        $contents[] = new restore_decode_content('stopwatch', array('intro'), 'stopwatch');

        return $contents;
    }

    /**
     * Define the decoding rules for links belonging
     * to the activity to be executed by the link decoder
     */
    static public function define_decode_rules() {
        $rules = array();

        $rules[] = new restore_decode_rule('STOPWATCHVIEWBYID', '/mod/stopwatch/view.php?id=$1', 'course_module');
        $rules[] = new restore_decode_rule('STOPWATCHINDEX', '/mod/stopwatch/index.php?id=$1', 'course');

        return $rules;

    }

    /**
     * Define the restore log rules that will be applied
     * by the {@link restore_logs_processor} when restoring
     * stopwatch logs. It must return one array
     * of {@link restore_log_rule} objects
     */
    static public function define_restore_log_rules() {
        $rules = array();

        $rules[] = new restore_log_rule('stopwatch', 'add', 'view.php?id={course_module}', '{stopwatch}');
        $rules[] = new restore_log_rule('stopwatch', 'update', 'view.php?id={course_module}', '{stopwatch}');
        $rules[] = new restore_log_rule('stopwatch', 'view', 'view.php?id={course_module}', '{stopwatch}');

        return $rules;
    }

    /**
     * Define the restore log rules that will be applied
     * by the {@link restore_logs_processor} when restoring
     * course logs. It must return one array
     * of {@link restore_log_rule} objects
     *
     * Note this rules are applied when restoring course logs
     * by the restore final task, but are defined here at
     * activity level. All them are rules not linked to any module instance (cmid = 0)
     */
    static public function define_restore_log_rules_for_course() {
        $rules = array();

        $rules[] = new restore_log_rule('stopwatch', 'view all', 'index.php?id={course}', null);

        return $rules;
    }
}
