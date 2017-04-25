<?php

/**
 * ************************************************************************
 * *                  Saint Boniface - TRADUCTION                        **
 * ************************************************************************
 * @package     theme                                                    **
 * @subpackage  boniface_trad                                            **
 * @name        boniface_trad                                            **
 * @copyright   oohoo.biz                                                **
 * @link        http://oohoo.biz                                         **
 * @author      Nicolas Bretin                                           **
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later **
 * ************************************************************************
 * ************************************************************************ */
class theme_c_eje011_core_renderer extends core_renderer
{

    /**
     * Constructor
     * @param \moodle_page $page
     * @param type $target
     */
    public function __construct(\moodle_page $page, $target)
    {
        //Force popup layout if param popup is active
        $popup = optional_param('popup', 0, PARAM_INT);
        $embed = optional_param('embed', 0, PARAM_INT);
        $embedded = optional_param('embedded', 0, PARAM_INT);
        if ($popup == 1 || $embed == 1 || $embedded == 1)
        {
            $page->set_pagelayout('embedded');
        }

        parent::__construct($page, $target);
    }

    /**
     * Prints a nice side block with an optional header.
     *
     * The content is described
     * by a {@link block_contents} object.
     *
     * @param block_contents $bc HTML for the content
     * @param string $region the region the block is appearing in.
     * @return string the HTML to be output.
     */
    function block(block_contents $bc, $region)
    {

        $bc = clone($bc); // Avoid messing up the object passed in.
        if (empty($bc->blockinstanceid) || !strip_tags($bc->title))
        {
            $bc->collapsible = block_contents::NOT_HIDEABLE;
        }
        if ($bc->collapsible == block_contents::HIDDEN)
        {
            $bc->add_class('hidden');
        }
        if (!empty($bc->controls))
        {
            $bc->add_class('block_with_controls');
        }
        //Force the dock
        //$bc->add_class('dock_on_load');
        
        $skiptitle = strip_tags($bc->title);
        if (empty($skiptitle))
        {
            $output = '';
            $skipdest = '';
        }
        else
        {
            $output = html_writer::tag('a', get_string('skipa', 'access', $skiptitle), array('href' => '#sb-' . $bc->skipid, 'class' => 'skip-block'));
            $skipdest = html_writer::tag('span', '', array('id' => 'sb-' . $bc->skipid, 'class' => 'skip-block-to'));
        }

        $output .= html_writer::start_tag('div', $bc->attributes);

        /** Rounded corners * */
        $output .= html_writer::start_tag('div', array());

        $controlshtml = $this->block_controls($bc->controls);

        $title = '';
        if ($bc->title)
        {
            $title = html_writer::tag('h2', $bc->title);
        }

        if ($title || $controlshtml)
        {
            $output .= html_writer::tag('div', html_writer::tag('div', html_writer::tag('div', '', array('class' => 'block_action')) . $title . $controlshtml, array('class' => 'title')), array('class' => 'header'));
        }

        $output .= html_writer::start_tag('div', array('class' => 'content'));
        if (!$title && !$controlshtml)
        {
            $output .= html_writer::tag('div', '', array('class' => 'block_action notitle'));
        }
        $output .= $bc->content;

        if ($bc->footer)
        {
            $output .= html_writer::tag('div', $bc->footer, array('class' => 'footer'));
        }

        $output .= html_writer::end_tag('div');

        /** Four rounded corner ends * */
        $output .= html_writer::start_tag('div', array('class' => 'blockend')) . html_writer::end_tag('div');
        $output .= html_writer::end_tag('div');

        $output .= html_writer::end_tag('div');

        if ($bc->annotation)
        {
            $output .= html_writer::tag('div', $bc->annotation, array('class' => 'blockannotation'));
        }
        $output .= $skipdest;

        $this->init_block_hider_js($bc);

        return $output;
    }

    /**
     * Returns the custom menu if one has been set
     *
     * A custom menu can be configured by browsing to
     *    Settings: Administration > Appearance > Themes > Theme settings
     * and then configuring the custommenu config setting as described.
     *
     * @param string $custommenuitems - custom menuitems set by theme instead of global theme settings
     * @return string
     */
    public function custom_menu($custommenuitems = '')
    {
        global $CFG;
        if (empty($custommenuitems) && !empty($CFG->custommenuitems))
        {
            $custommenuitems = $CFG->custommenuitems;
        }
        $custommenu = new custom_menu($custommenuitems, current_language());
        return $this->render_custom_menu($custommenu);
    }

    /**
     * Renders a custom menu object (located in outputcomponents.php)
     *
     * The custom menu this method override the render_custom_menu function
     * in outputrenderers.php
     * @staticvar int $menucount
     * @param custom_menu $menu
     * @return string
     */
    protected function render_custom_menu(custom_menu $menu)
    {
        global $CFG, $PAGE, $COURSE;
        $courseinfo = course_get_format($COURSE)->get_course();
        require_once($CFG->dirroot . DIRECTORY_SEPARATOR . 'course' . DIRECTORY_SEPARATOR . 'format' . DIRECTORY_SEPARATOR . 'lib.php');

        // If the menu has no children return an empty string
        if ($courseinfo->id == 1 && !$menu->has_children())
        {
            return '';
        }

        if ($courseinfo->id != 1)
        {
            //$format = course_get_format($COURSE);
            //$renderer = $PAGE->get_renderer(get_class($format));
            $modinfo = get_fast_modinfo($courseinfo);
            $sections = $modinfo->get_section_info_all();

            //Remove section 0
            unset($sections[0]);

            //Get the last sort position
            $position = 1;
            $items = $menu->get_children();
            if (count($items) > 0)
            {
                $lastitem = array_pop($items);
                $position = $lastitem->get_sort_order() + 1;
            }

            foreach ($sections as $section)
            {
                //If the section position is greater than the number of sections to display, continue
                if($section->section > $courseinfo->numsections)
                {
                    continue;
                }
                $branchlabel = get_section_name($courseinfo, $section);
                $branchurl = course_get_url($courseinfo, $section->section, array('navigation' => true));
                $branch = $menu->add($branchlabel, $branchurl, $branchlabel, $position);
                $position++;
                $levels = array();
                $levels[0] = $branch;
                if (isset($modinfo->sections[$section->section]))
                {
                    //Add the modules of the section 0
                    foreach ($modinfo->sections[$section->section] as $modnumber)
                    {
                        $mod = $modinfo->cms[$modnumber];
                        $branchlabel = $mod->name;
                        $branchurl = new moodle_url($mod->get_url());

                        $level = $levels[0];
                        if (isset($levels[$mod->indent]))
                        {
                            $level = $levels[$mod->indent];
                        }
                        //In our special case only show items from the first level
                        if($level ==  $levels[0])
                        {
                            $branch = $level->add($branchlabel, $branchurl, $branchlabel, $position);
                        }

                        $levels = array_slice($levels, 0, $mod->indent + 1);
                        $levels[$mod->indent + 1] = $branch;
                        $position++;
                    }
                }
            }
        }

        // Initialise this custom menu
        $content = html_writer::start_tag('ul', array('class' => 'dropdown dropdown-horizontal'));
        // Render each child
        foreach ($menu->get_children() as $item)
        {
            $content .= $this->render_custom_menu_item($item);
        }
        // Close the open tags
        $content .= html_writer::end_tag('ul');
        // Return the custom menu
        return $content;
    }

    /**
     * Renders a custom menu node as part of a submenu
     *
     * The custom menu this method override the render_custom_menu_item function
     * in outputrenderers.php
     *
     * @see render_custom_menu()
     *
     * @staticvar int $submenucount
     * @param custom_menu_item $menunode
     * @return string
     */
    protected function render_custom_menu_item(custom_menu_item $menunode)
    {
        // Required to ensure we get unique trackable id's
        static $submenucount = 0;
        $content = html_writer::start_tag('li');
        if ($menunode->has_children())
        {
            // If the child has menus render it as a sub menu
            $submenucount++;
            if ($menunode->get_url() !== null)
            {
                $url = $menunode->get_url();
            }
            else
            {
                $url = '#cm_submenu_' . $submenucount;
            }
            $content .= html_writer::start_tag('span', array('class' => 'customitem'));
            $content .= html_writer::link($url, $menunode->get_text(), array('title' => $menunode->get_title()));
            $content .= html_writer::end_tag('span');
            $content .= html_writer::start_tag('ul');
            foreach ($menunode->get_children() as $menunode)
            {
                $content .= $this->render_custom_menu_item($menunode);
            }
            $content .= html_writer::end_tag('ul');
        }
        else
        {
            // The node doesn't have children so produce a final menuitem

            if ($menunode->get_url() !== null)
            {
                $url = $menunode->get_url();
            }
            else
            {
                $url = '#';
            }
            $content .= html_writer::link($url, $menunode->get_text(), array('title' => $menunode->get_title()));
        }
        $content .= html_writer::end_tag('li');
        // Return the sub menu
        return $content;
    }

    /**
     * Generate a special block for the theme
     * @global stdClass $COURSE
     * @global moodle_database $DB
     * @global moodle_page $PAGE
     * @return string
     */
    public function get_content_customblock()
    {
        global $PAGE, $COURSE, $DB;
        $content = '';
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

                    $content .= html_writer::start_tag('div', array('class' => 'customblock'));
                    $content .= html_writer::start_tag('div', array('class' => 'customtitle'));
                    $content .= html_writer::start_tag('h1');
                    $content .= get_section_name($COURSE, $section);
                    $content .= html_writer::end_tag('h1');
                    $content .= html_writer::end_tag('div');
                    $content .= html_writer::start_tag('div', array('class' => 'customhead'));
                    $content .= html_writer::end_tag('div');
                    $content .= html_writer::start_tag('div', array('class' => 'customcontent'));
                    $content .= html_writer::start_tag('ul');
                    if (isset($modinfo->sections[$section->section]))
                    {
                        foreach ($modinfo->sections[$section->section] as $modnumber)
                        {
                            $mod = $modinfo->cms[$modnumber];
                            $content .= '<li><a href="' . $mod->get_url() . '">' . $mod->name . '</li>';
                        }
                    }

                    $content .= html_writer::end_tag('ul');
                    $content .= html_writer::end_tag('div');
                    $content .= html_writer::start_tag('div', array('class' => 'customfooter'));
                    $content .= html_writer::end_tag('div');

                    $content .= html_writer::end_tag('div');
                }
            }
        }
        return $content;
    }

}