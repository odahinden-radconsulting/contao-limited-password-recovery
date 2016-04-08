<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2015 Leo Feyer
 *
 * @package   RadExtensions
 * @author    Olivier Dahinden <o.dahinden@rad-consulting.ch>
 * @license   GNU
 * @copyright 2016
 */


/**
 * Table tl_module
 */

// palettes
$GLOBALS['TL_DCA']['tl_module']['palettes']['lostPasswordLimitedAccess'] = '{title_legend},name,headline,type;{config_legend},reg_skipName,disableCaptcha;{redirect_legend},jumpTo;{email_legend:hide},reg_jumpTo,reg_password;{limited_access_legend},limitedAccess;{template_legend:hide},customTpl,tableless;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space';

$GLOBALS['TL_DCA']['tl_module']['palettes']['__selector__'][] = 'limitedAccess';
$GLOBALS['TL_DCA']['tl_module']['subpalettes']['limitedAccess'] = 'limitGroups';
$GLOBALS['TL_DCA']['tl_module']['fields']['limitedAccess'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_module']['limitedAccess'],
    'exclude'                 => true,
    'filter'                  => true,
    'inputType'               => 'checkbox',
    'eval'                    => array('submitOnChange'=>true),
    'sql'                     => "char(1) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['limitGroups'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_module']['limitGroups'],
    'exclude'                 => true,
    'inputType'               => 'checkbox',
    'foreignKey'              => 'tl_member_group.name',
    'eval'                    => array('mandatory'=>true, 'multiple'=>true),
    'sql'                     => "blob NULL",
    'relation'                => array('type'=>'hasMany', 'load'=>'lazy')
);
