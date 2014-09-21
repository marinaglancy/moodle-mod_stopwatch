<?php

/**
 * Define all the backup steps that will be used by the backup_stopwatch_activity_task
 *
 * @package   mod_stopwatch
 * @category  backup
 * @copyright 2014 Marina Glancy
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

/**
 * Define the complete stopwatch structure for backup, with file and id annotations
 *
 * @package   mod_stopwatch
 * @category  backup
 * @copyright 2014 Marina Glancy
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class backup_stopwatch_activity_structure_step extends backup_activity_structure_step {

    protected function define_structure() {

        // To know if we are including userinfo
        $userinfo = $this->get_setting_value('userinfo');

        // Define each element separated
        $stopwatch = new backup_nested_element('stopwatch', array('id'), array(
            'name', 'intro', 'introformat', 'completiontimed', 'grade'));

        // Build the tree

        // Define sources
        $stopwatch->set_source_table('stopwatch', array('id' => backup::VAR_ACTIVITYID));

        // Define id annotations

        // Define file annotations
        $stopwatch->annotate_files('mod_stopwatch', 'intro', null); // This file areas haven't itemid

        // Return the root element (stopwatch), wrapped into standard activity structure
        return $this->prepare_activity_structure($stopwatch);
    }
}
