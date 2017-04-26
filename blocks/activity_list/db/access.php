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
defined('MOODLE_INTERNAL') || die();

$capabilities = array(
    'block/activity_list:myaddinstance' => array(
        'captype' => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes' => array(
            'user' => CAP_PREVENT
        ),
        'clonepermissionsfrom' => 'moodle/my:manageblocks'
    ),
    'block/activity_list:addinstance' => array(
        'riskbitmask' => RISK_SPAM | RISK_XSS,
        'captype' => 'write',
        'contextlevel' => CONTEXT_BLOCK,
        'archetypes' => array(
            'editingteacher' => CAP_ALLOW,
            'manager' => CAP_ALLOW
        ),
        'clonepermissionsfrom' => 'moodle/site:manageblocks'
    ),
);
