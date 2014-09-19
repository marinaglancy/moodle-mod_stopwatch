<?php

/**
 * Define all the restore steps that will be used by the restore_stopwatch_activity_task
 *
 * @package   mod_stopwatch
 * @category  backup
 * @copyright 2014 Marina Glancy
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Structure step to restore one stopwatch activity
 *
 * @package   mod_stopwatch
 * @category  backup
 * @copyright 2014 Marina Glancy
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restore_stopwatch_activity_structure_step extends restore_activity_structure_step {

    protected function define_structure() {

        $paths = array();
        $paths[] = new restore_path_element('stopwatch', '/activity/stopwatch');

        // Return the paths wrapped into standard activity structure
        return $this->prepare_activity_structure($paths);
    }

    protected function process_stopwatch($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        $data->course = $this->get_courseid();

        if (empty($data->timecreated)) {
            $data->timecreated = time();
        }

        if (empty($data->timemodified)) {
            $data->timemodified = time();
        }

        // insert the stopwatch record
        $newitemid = $DB->insert_record('stopwatch', $data);
        // immediately after inserting "activity" record, call this
        $this->apply_activity_instance($newitemid);
    }

    protected function after_execute() {
        // Add stopwatch related files, no need to match by itemname (just internally handled context)
        $this->add_related_files('mod_stopwatch', 'intro', null);
    }
}
