<?php

/**
 * mod_stopwatch tests.
 *
 * @package    mod_stopwatch
 * @copyright  2014 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

global $CFG;
require_once($CFG->dirroot . '/mod/stopwatch/locallib.php');

/**
 * mod_stopwatch test class
 *
 * @package    mod_stopwatch
 * @copyright  2014 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_stopwatch_generator_testcase extends advanced_testcase {
    public function test_mod_stopwatch_string_to_duration() {
        $H = 3600;
        $M = 60;
        $this->assertEquals($H + 15 * $M + 20, mod_stopwatch_string_to_duration('1:15:20'));
        $this->assertEquals(4 * $M + 59, mod_stopwatch_string_to_duration('0:04:59'));
        $this->assertEquals(2, mod_stopwatch_string_to_duration('00:00:02'));
        $this->assertEquals(2, mod_stopwatch_string_to_duration('0:02'));
        $this->assertEquals(2, mod_stopwatch_string_to_duration('2'));
        $this->assertEquals(58, mod_stopwatch_string_to_duration('00:58'));
        $this->assertEquals(58, mod_stopwatch_string_to_duration('00:00:58'));
    }

    public function test_mod_stopwatch_duration_to_string() {
        $H = 3600;
        $M = 60;
        $this->assertEquals('01:15:20', mod_stopwatch_duration_to_string($H + 15 * $M + 20));
        $this->assertEquals('00:04:59', mod_stopwatch_duration_to_string(4 * $M + 59));
        $this->assertEquals('00:00:02', mod_stopwatch_duration_to_string(2));
        $this->assertEquals('00:00:58', mod_stopwatch_duration_to_string(58));
    }
}
