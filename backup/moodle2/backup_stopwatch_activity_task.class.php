<?php

/**
 * Defines backup_stopwatch_activity_task class
 *
 * @package   mod_stopwatch
 * @category  backup
 * @copyright 2014 Marina Glancy
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot . '/mod/stopwatch/backup/moodle2/backup_stopwatch_stepslib.php');

/**
 * Provides the steps to perform one complete backup of the Stopwatch instance
 *
 * @package   mod_stopwatch
 * @category  backup
 * @copyright 2014 Marina Glancy
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class backup_stopwatch_activity_task extends backup_activity_task {

    /**
     * No specific settings for this activity
     */
    protected function define_my_settings() {
    }

    /**
     * Defines a backup step to store the instance data in the stopwatch.xml file
     */
    protected function define_my_steps() {
        $this->add_step(new backup_stopwatch_activity_structure_step('stopwatch_structure', 'stopwatch.xml'));
    }

    /**
     * Encodes URLs to the index.php and view.php scripts
     *
     * @param string $content some HTML text that eventually contains URLs to the activity instance scripts
     * @return string the content with the URLs encoded
     */
    static public function encode_content_links($content) {
        global $CFG;

        $base = preg_quote($CFG->wwwroot,"/");

        // Link to the list of stopwatches
        $search="/(".$base."\/mod\/stopwatch\/index.php\?id\=)([0-9]+)/";
        $content= preg_replace($search, '$@STOPWATCHINDEX*$2@$', $content);

        // Link to stopwatch view by moduleid
        $search="/(".$base."\/mod\/stopwatch\/view.php\?id\=)([0-9]+)/";
        $content= preg_replace($search, '$@STOPWATCHVIEWBYID*$2@$', $content);

        return $content;
    }
}
