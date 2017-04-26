<?php

/**
 * ************************************************************************
 * *                  Saint Boniface                                     **
 * ************************************************************************
 * @package     format                                                   **
 * @subpackage  boniface                                                 **
 * @name        boniface                                                 **
 * @copyright   oohoo.biz                                                **
 * @link        http://oohoo.biz                                         **
 * @author      Nicolas Bretin                                           **
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later **
 * ************************************************************************
 * ************************************************************************ */
defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/course/format/topics/renderer.php');

/**
 * Basic renderer inherit from topics format.
 *
 * @copyright 2012 Dan Poltawski
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class format_boniface_renderer extends format_topics_renderer
{

    /**
     * Output the html for a single section page .
     *
     * @param stdClass $course The course entry from DB
     * @param array $sections (argument not used)
     * @param array $mods (argument not used)
     * @param array $modnames (argument not used)
     * @param array $modnamesused (argument not used)
     * @param int $displaysection The section number in the course which is being displayed
     */
    public function print_single_section0_page($course)
    {
        global $PAGE;

        $displaysection = 0;
        $modinfo = get_fast_modinfo($course);
        $course = course_get_format($course)->get_course();

        // Can we view the section in question?
        if (!($sectioninfo = $modinfo->get_section_info($displaysection)))
        {
            // This section doesn't exist
            print_error('unknowncoursesection', 'error', null, $course->fullname);
            return;
        }

        if (!$sectioninfo->uservisible)
        {
            if (!$course->hiddensections)
            {
                echo $this->start_section_list();
                echo $this->section_hidden($displaysection);
                echo $this->end_section_list();
            }
            // Can't view this section.
            return;
        }

        //Print the section 0
        $thissection = $modinfo->get_section_info($displaysection);
        if ($thissection->summary or !empty($modinfo->sections[$displaysection]))
        {
            //Check if the summary exists or have at least a link
            if($thissection->summary == '' || stripos($thissection->summary, '<a ') === false)
            {
                $thissection->summary .= '<br/><b>'.get_string('summary_need_link', 'format_boniface').'</b>';
            }
            echo $this->start_section_list();
            echo $this->section_header($thissection, $course, true, $displaysection);
            //print_section($course, $thissection, null, null, true, "100%", false, $displaysection);
            echo $this->section_footer();
            echo $this->end_section_list();
        }
    }

}
