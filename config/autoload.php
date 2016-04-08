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
 * Register the namespaces
 */
ClassLoader::addNamespaces(array
(
    'Contao',
));
/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
    'Contao\ModulePasswordLimitedAccess'            => 'system/modules/contao-limited-password-recovery/modules/ModulePasswordLimitedAccess.php',
));