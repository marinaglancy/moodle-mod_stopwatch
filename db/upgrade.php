<?php

/**
 * This file keeps track of upgrades to the stopwatch module
 *
 * Sometimes, changes between versions involve alterations to database
 * structures and other major things that may break installations. The upgrade
 * function in this file will attempt to perform all the necessary actions to
 * upgrade your older installation to the current version. If there's something
 * it cannot do itself, it will tell you what you need to do.  The commands in
 * here will all be database-neutral, using the functions defined in DLL libraries.
 *
 * @package    mod_stopwatch
 * @copyright  2014 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Execute stopwatch upgrade from the given old version
 *
 * @param int $oldversion
 * @return bool
 */
function xmldb_stopwatch_upgrade($oldversion) {
    global $DB, $CFG;

    $dbman = $DB->get_manager(); // loads ddl manager and xmldb classes

    if ($oldversion < 2014091103) {

        // Define field completiontimed to be added to stopwatch.
        $table = new xmldb_table('stopwatch');
        $field = new xmldb_field('completiontimed', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'timemodified');

        // Conditionally launch add field completiontimed.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Stopwatch savepoint reached.
        upgrade_mod_savepoint(true, 2014091103, 'stopwatch');
    }

    if ($oldversion < 2014091104) {

        // Define table stopwatch_timing to be created.
        $table = new xmldb_table('stopwatch_timing');

        // Adding fields to table stopwatch_timing.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('courseid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('stopwatchid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('duration', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');

        // Adding keys to table stopwatch_timing.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for stopwatch_timing.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Stopwatch savepoint reached.
        upgrade_mod_savepoint(true, 2014091104, 'stopwatch');
    }

    if ($oldversion < 2014092100) {

        // Define field grade to be added to stopwatch.
        $table = new xmldb_table('stopwatch');
        $field = new xmldb_field('grade', XMLDB_TYPE_NUMBER, '10, 5', null, null, null, '100', 'completiontimed');

        // Conditionally launch add field grade.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Stopwatch savepoint reached.
        upgrade_mod_savepoint(true, 2014092100, 'stopwatch');
    }

    if ($oldversion < 2014092101) {

        // Define table stopwatch_timing to be renamed to NEWNAMEGOESHERE.
        $table = new xmldb_table('stopwatch_timing');

        // Launch rename table for stopwatch_timing.
        $dbman->rename_table($table, 'stopwatch_user');

        // Stopwatch savepoint reached.
        upgrade_mod_savepoint(true, 2014092101, 'stopwatch');
    }

    if ($oldversion < 2014092102) {

        // Define index stopwatchuser (unique) to be added to stopwatch_user.
        $table = new xmldb_table('stopwatch_user');
        $index = new xmldb_index('stopwatchuser', XMLDB_INDEX_UNIQUE, array('courseid', 'stopwatchid', 'userid'));

        // Conditionally launch add index stopwatchuser.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Stopwatch savepoint reached.
        upgrade_mod_savepoint(true, 2014092102, 'stopwatch');
    }

    if ($oldversion < 2014092103) {

        // Define field grade to be added to stopwatch_user.
        $table = new xmldb_table('stopwatch_user');
        $field = new xmldb_field('grade', XMLDB_TYPE_NUMBER, '10, 5', null, null, null, null, 'duration');

        // Conditionally launch add field grade.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('timegraded', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'grade');
        // Conditionally launch add field timegraded.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Stopwatch savepoint reached.
        upgrade_mod_savepoint(true, 2014092103, 'stopwatch');
    }

    if ($oldversion < 2014092104) {
        require_once($CFG->libdir.'/gradelib.php');
        $records = $DB->get_records('stopwatch');
        foreach ($records as $stopwatch) {
            $item = array();
            $item['itemname'] = clean_param($stopwatch->name, PARAM_NOTAGS);
            $item['gradetype'] = GRADE_TYPE_VALUE;
            $item['grademax']  = $stopwatch->grade;
            $item['grademin']  = 0;
            grade_update('mod/stopwatch', $stopwatch->course, 'mod', 'stopwatch', $stopwatch->id, 0, null, $item);
        }

        // Stopwatch savepoint reached.
        upgrade_mod_savepoint(true, 2014092104, 'stopwatch');
    }
    if ($oldversion < 2014092106) {

        // Changing type of field grade on table stopwatch to int.
        $table = new xmldb_table('stopwatch');
        $field = new xmldb_field('grade', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '100', 'completiontimed');

        // Launch change of type for field grade.
        $dbman->change_field_type($table, $field);

        // Stopwatch savepoint reached.
        upgrade_mod_savepoint(true, 2014092106, 'stopwatch');
    }

    return true;
}
