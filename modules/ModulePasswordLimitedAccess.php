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

namespace Contao;


/**
 * Front end module "lost password".
 *
 * @author Leo Feyer <https://github.com/leofeyer>
 */
class ModulePasswordLimitedAccess extends \ModulePassword
{

    /**
     * Template
     * @var string
     */
    protected $strTemplate = 'mod_password';


    /**
     * Display a wildcard in the back end
     *
     * @return string
     */
    public function generate()
    {
        if (TL_MODE == 'BE')
        {
            /** @var \BackendTemplate|object $objTemplate */
            $objTemplate = new \BackendTemplate('be_wildcard');

            $objTemplate->wildcard = '### ' . utf8_strtoupper($GLOBALS['TL_LANG']['FMD']['lostPasswordLimitedAccess'][0]) . ' ###';
            $objTemplate->title = $this->headline;
            $objTemplate->id = $this->id;
            $objTemplate->link = $this->name;
            $objTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

            return $objTemplate->parse();
        }

        return parent::generate();
    }


    /**
     * Generate the module
     */
    protected function compile()
    {
        /** @var \PageModel $objPage */
        global $objPage;

        $GLOBALS['TL_LANGUAGE'] = $objPage->language;

        \System::loadLanguageFile('tl_member');
        $this->loadDataContainer('tl_member');

        // Set new password
        if (strlen(\Input::get('token')))
        {
            $this->setNewPassword();

            return;
        }

        // Username widget
        if (!$this->reg_skipName)
        {
            $arrFields['username'] = $GLOBALS['TL_DCA']['tl_member']['fields']['username'];
            $arrFields['username']['name'] = 'username';
        }

        // E-mail widget
        $arrFields['email'] = $GLOBALS['TL_DCA']['tl_member']['fields']['email'];
        $arrFields['email']['name'] = 'email';

        // Captcha widget
        if (!$this->disableCaptcha)
        {
            $arrFields['captcha'] = array
            (
                'name' => 'lost_password',
                'label' => $GLOBALS['TL_LANG']['MSC']['securityQuestion'],
                'inputType' => 'captcha',
                'eval' => array('mandatory'=>true)
            );
        }

        $row = 0;
        $strFields = '';
        $doNotSubmit = false;

        // Initialize the widgets
        foreach ($arrFields as $arrField)
        {
            /** @var \Widget $strClass */
            $strClass = $GLOBALS['TL_FFL'][$arrField['inputType']];

            // Continue if the class is not defined
            if (!class_exists($strClass))
            {
                continue;
            }

            $arrField['eval']['tableless'] = $this->tableless;
            $arrField['eval']['required'] = $arrField['eval']['mandatory'];

            /** @var \Widget $objWidget */
            $objWidget = new $strClass($strClass::getAttributesFromDca($arrField, $arrField['name']));

            $objWidget->storeValues = true;
            $objWidget->rowClass = 'row_' . $row . (($row == 0) ? ' row_first' : '') . ((($row % 2) == 0) ? ' even' : ' odd');
            ++$row;

            // Validate the widget
            if (\Input::post('FORM_SUBMIT') == 'tl_lost_password')
            {
                $objWidget->validate();

                if ($objWidget->hasErrors())
                {
                    $doNotSubmit = true;
                }
            }

            $strFields .= $objWidget->parse();
        }

        $this->Template->fields = $strFields;
        $this->Template->hasError = $doNotSubmit;


        // Look for an account and send the password link
        if (\Input::post('FORM_SUBMIT') == 'tl_lost_password' && !$doNotSubmit)
        {
            if ($this->reg_skipName)
            {
                $objMember = \MemberModel::findActiveByEmailAndUsername(\Input::post('email', true), null);
            }
            else
            {
                $objMember = \MemberModel::findActiveByEmailAndUsername(\Input::post('email', true), \Input::post('username'));
            }

            if ($objMember === null)
            {
                sleep(2); // Wait 2 seconds while brute forcing :)
                $this->Template->error = $GLOBALS['TL_LANG']['MSC']['accountNotFound'];
            }
            else
            {
                if ($this->limitedAccess) {
                    $groups = unserialize($this->limitGroups);
                    $member_groups = unserialize($objMember->groups);

                    foreach($groups as $group) {
                        if (in_array($group, $member_groups)) {
                            $doNotSubmit = true;
                            $this->Template->error = $GLOBALS['TL_LANG']['MSC']['passwort_reset_not_allowed'];
                            break;
                        }
                    }
                }

                if (!$doNotSubmit) {
                    $this->sendPasswordLink($objMember);
                }
            }
        }

        $this->Template->formId = 'tl_lost_password';
        $this->Template->username = specialchars($GLOBALS['TL_LANG']['MSC']['username']);
        $this->Template->email = specialchars($GLOBALS['TL_LANG']['MSC']['emailAddress']);
        $this->Template->action = \Environment::get('indexFreeRequest');
        $this->Template->slabel = specialchars($GLOBALS['TL_LANG']['MSC']['requestPassword']);
        $this->Template->rowLast = 'row_' . $row . ' row_last' . ((($row % 2) == 0) ? ' even' : ' odd');
        $this->Template->tableless = $this->tableless;
    }
}