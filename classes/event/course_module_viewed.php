<?php
/**
 * The mod_stopwatch course module viewed event.
 *
 * @package    mod_stopwatch
 * @copyright  2014 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_stopwatch\event;

defined('MOODLE_INTERNAL') || die();

/**
 * The mod_stopwatch course module viewed event class.
 *
 * @package    mod_stopwatch
 * @copyright  2014 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_module_viewed extends \core\event\course_module_viewed {

    /**
     * Init method.
     */
    protected function init() {
        $this->data['crud'] = 'r';
        $this->data['edulevel'] = self::LEVEL_PARTICIPATING;
        $this->data['objecttable'] = 'stopwatch';
    }

    public static function create_from_cm(\cm_info $cm, $course, $stopwatch = null) {
        $event = \mod_folder\event\course_module_viewed::create(array(
            'context' => \context_module::instance($cm->id),
            'objectid' => $cm->instance
        ));
        $event->add_record_snapshot('course_modules', $cm);
        $event->add_record_snapshot('course', $course);
        if ($stopwatch) {
            $event->add_record_snapshot('stopwatch', $stopwatch);
        }
        return $event;
    }
}
