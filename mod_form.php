<?php

/**
 * The main stopwatch configuration form
 *
 * It uses the standard core Moodle formslib. For more info about them, please
 * visit: http://docs.moodle.org/en/Development:lib/formslib.php
 *
 * @package    mod_stopwatch
 * @copyright  2014 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/course/moodleform_mod.php');

/**
 * Module instance settings form
 */
class mod_stopwatch_mod_form extends moodleform_mod {

    /**
     * Defines forms elements
     */
    public function definition() {
        global $CFG;

        $mform = $this->_form;

        //-------------------------------------------------------------------------------
        // Adding the "general" fieldset, where all the common settings are showed
        $mform->addElement('header', 'general', get_string('general', 'form'));

        // Adding the standard "name" field
        $mform->addElement('text', 'name', get_string('name', 'stopwatch'), array('size'=>'64'));
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEAN);
        }
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        //$mform->addHelpButton('name', 'name', 'stopwatch');

        // Adding the standard "intro" and "introformat" fields
        $this->add_intro_editor();
/*
        //-------------------------------------------------------------------------------
        // Adding the rest of stopwatch settings, spreeading all them into this fieldset
        // or adding more fieldsets ('header' elements) if needed for better logic
        $mform->addElement('static', 'label1', 'stopwatchsetting1', 'Your stopwatch fields go here. Replace me!');

        $mform->addElement('header', 'stopwatchfieldset', get_string('stopwatchfieldset', 'stopwatch'));
        $mform->addElement('static', 'label2', 'stopwatchsetting2', 'Your stopwatch fields go here. Replace me!');
*/
        //-------------------------------------------------------------------------------
        // add standard elements, common to all modules
        $this->standard_coursemodule_elements();
        //-------------------------------------------------------------------------------
        // add standard buttons, common to all modules
        $this->add_action_buttons();
    }

    /**
     * Custom completion rules
     *
     * @return array Array of string IDs of added items, empty array if none
     */
    public function add_completion_rules() {
        $mform = $this->_form;

        $mform->addElement('checkbox', 'completiontimed', get_string('completiontimed', 'stopwatch'),
                get_string('completiontimed_desc', 'stopwatch'));
        $mform->disabledIf('completionposts','completionpostsenabled', 'notchecked');

        return array('completiontimed');
    }

    /**
     * Called during validation. Indicates whether
     * a custom completion rule is enabled (selected).
     *
     * @param array $data Input data (not yet validated)
     * @return bool True if one or more rules is enabled, false if none are;
     *   default returns false
     */
    public function completion_rule_enabled($data) {
        return (!empty($data['completiontimed']));
    }
}
