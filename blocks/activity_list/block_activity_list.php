<?php

/**
 * ************************************************************************
 * *                  Activity list by section                           **
 * ************************************************************************
 * @package     block                                                    **
 * @subpackage  activity_list                                            **
 * @name        activity_list                                            **
 * @copyright   oohoo.biz                                                **
 * @link        http://oohoo.biz                                         **
 * @author      Nicolas Bretin                                           **
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later **
 * ************************************************************************
 * ************************************************************************ */
include_once($CFG->dirroot . '/course/lib.php');

class block_activity_list extends block_list
{

    function init()
    {
        $this->title = get_string('pluginname', 'block_activity_list');
    }

    public function applicable_formats()
    {
        return array(
            '*' => true,
            'course-*' => true,
            'course-view' => true,
            'mod' => true);
    }

    function has_config()
    {
        return true;
    }

    /**
     * Do any additional initialization you may need at the time a new block instance is created
     * @global moodle_database $DB
     * @return boolean
     */
    function instance_create()
    {
        global $DB;
        $this->instance->showinsubcontexts = 1;
        $this->instance->pagetypepattern = '*';
        $this->instance->defaultweight = -10;
        $DB->update_record('block_instances', $this->instance);
        return true;
    }
    
    public function instance_can_be_docked()
    {
        return parent::instance_can_be_docked();
    }

    function get_content()
    {
        global $CFG, $USER, $DB, $OUTPUT, $COURSE, $PAGE;

        if ($this->content !== NULL)
        {
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->items = array();
        $this->content->icons = array();
        $this->content->footer = '';


        if ($COURSE->id != 1)
        {
            //Try to get the section number
            $sectionnum = optional_param('section', 0, PARAM_INT);
            //if sectionnum not founded or 0 get the module ans get the section from the module
            if ($sectionnum == 0)
            {
                if ($PAGE->context->get_level_name() == get_string('activitymodule'))
                {
                    $coursemodule = $DB->get_record('course_modules', array('id' => $PAGE->context->instanceid));
                    $section = $DB->get_record('course_sections', array('id' => $coursemodule->section));
                    $sectionnum = $section->section;
                }
            }

            if ($sectionnum > 0)
            {
                //$format = course_get_format($COURSE);
                //$renderer = $PAGE->get_renderer(get_class($format));
                $modinfo = get_fast_modinfo($COURSE);

                if ($sectionnum != 0)
                {
                    $section = $modinfo->get_section_info($sectionnum);

                    $this->title = get_section_name($COURSE, $section);

                    if (isset($modinfo->sections[$section->section]))
                    {
                        foreach ($modinfo->sections[$section->section] as $modnumber)
                        {
                            $mod = $modinfo->cms[$modnumber];
                            $this->content->items[] = '<a href="' . $mod->get_url() . '">' . $mod->name . '</a>';
                            $this->content->icons[] = '';
                        }
                        $this->content->footer = '<div class="customfooter"></div>';
                    }
                }
            }
        }

        return $this->content;
    }

    /**
     * Returns the role that best describes the course list block.
     *
     * @return string
     */
    public function get_aria_role()
    {
        return 'navigation';
    }

}

