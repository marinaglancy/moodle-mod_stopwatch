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
    global $DB;

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

    return true;
}
