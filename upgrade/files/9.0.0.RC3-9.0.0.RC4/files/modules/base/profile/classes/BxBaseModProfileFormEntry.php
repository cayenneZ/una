<?php defined('BX_DOL') or die('hack attempt');
/**
 * Copyright (c) UNA, Inc - https://una.io
 * MIT License - https://opensource.org/licenses/MIT
 *
 * @defgroup    BaseProfile Base classes for profile modules
 * @ingroup     UnaModules
 *
 * @{
 */

/**
 * Create/edit profile form.
 */
class BxBaseModProfileFormEntry extends BxBaseModGeneralFormEntry
{
    protected $_iAccountProfileId = 0;
    protected $_aImageFields = array ();

    public function __construct($aInfo, $oTemplate = false)
    {
        parent::__construct($aInfo, $oTemplate);

        $CNF = &$this->_oModule->_oConfig->CNF;

        if (isset($this->aInputs[$CNF['FIELD_ALLOW_VIEW_TO']]) && $oPrivacy = BxDolPrivacy::getObjectInstance($CNF['OBJECT_PRIVACY_VIEW'])) {

            $aSave = array('db' => array('pass' => 'Xss'));
            array_walk($this->aInputs[$CNF['FIELD_ALLOW_VIEW_TO']], function ($a, $k, $aSave) {
                if (in_array($k, array('info', 'caption', 'value')))
                    $aSave[0][$k] = $a;
            }, array(&$aSave));
            
            $aGroupChooser = $oPrivacy->getGroupChooser($CNF['OBJECT_PRIVACY_VIEW']);
            
            $this->aInputs[$CNF['FIELD_ALLOW_VIEW_TO']] = array_merge($this->aInputs[$CNF['FIELD_ALLOW_VIEW_TO']], $aGroupChooser, $aSave);
		}

        if (!empty($CNF['FIELD_PICTURE']) && isset($this->aInputs[$CNF['FIELD_PICTURE']])) {
            $this->_aImageFields[$CNF['FIELD_PICTURE']] = array (
                'storage_object' => $CNF['OBJECT_STORAGE'],
                'images_transcoder' => $CNF['OBJECT_IMAGES_TRANSCODER_THUMB'],
                'uploaders' => $CNF['OBJECT_UPLOADERS_PICTURE'],
            );
        }

        if (!empty($CNF['FIELD_COVER']) && isset($this->aInputs[$CNF['FIELD_COVER']])) {
            $this->_aImageFields[$CNF['FIELD_COVER']] = array (
                'storage_object' => $CNF['OBJECT_STORAGE_COVER'],
                'images_transcoder' => $CNF['OBJECT_IMAGES_TRANSCODER_COVER_THUMB'],
                'uploaders' => $CNF['OBJECT_UPLOADERS_COVER'],
            );
        }

        foreach ($this->_aImageFields as $sField => $aParams) {
            $this->aInputs[$sField]['storage_object'] = $aParams['storage_object'];
            $this->aInputs[$sField]['uploaders'] = !empty($this->aInputs[$sField]['value']) ? unserialize($this->aInputs[$sField]['value']) : $aParams['uploaders'];
            $this->aInputs[$sField]['images_transcoder'] = $aParams['images_transcoder'];
            $this->aInputs[$sField]['storage_private'] = 0;
            $this->aInputs[$sField]['multiple'] = false;
            $this->aInputs[$sField]['content_id'] = 0;
            $this->aInputs[$sField]['ghost_template'] = '';
        }

        $oAccountProfile = BxDolProfile::getInstanceAccountProfile();
        if ($oAccountProfile)
            $this->_iAccountProfileId = $oAccountProfile->id();
    }

    function initChecker ($aValues = array (), $aSpecificValues = array())
    {
        $CNF = &$this->_oModule->_oConfig->CNF;

        $aContentInfo = isset($CNF['FIELD_ID']) && isset($aValues[$CNF['FIELD_ID']]) ? $this->_oModule->_oDb->getContentInfoById ($aValues[$CNF['FIELD_ID']]) : array();
        
        foreach ($this->_aImageFields as $sField => $aParams) {

            if ($aValues && !empty($aValues[$CNF['FIELD_ID']]))
                $this->aInputs[$sField]['content_id'] = $aValues[$CNF['FIELD_ID']];

            $this->aInputs[$sField]['ghost_template'] = $this->_oModule->_oTemplate->parseHtmlByName('form_ghost_template.html', $this->_getProfilePhotoGhostTmplVars($sField, $aContentInfo));
        }

        parent::initChecker($aValues, $aSpecificValues);
    }

    public function insert ($aValsToAdd = array(), $isIgnore = false)
    {
        $CNF = &$this->_oModule->_oConfig->CNF;

        return parent::insert ($aValsToAdd, $isIgnore);
    }

    function update ($iContentId, $aValsToAdd = array(), &$aTrackTextFieldsChanges = null)
    {
        $CNF = &$this->_oModule->_oConfig->CNF;

        return parent::update ($iContentId, $aValsToAdd, $aTrackTextFieldsChanges);
    }

    function delete ($iContentId, $aContentInfo = array())
    {
        $CNF = &$this->_oModule->_oConfig->CNF;

        $iProfileId = $this->getContentOwnerProfileId($iContentId);        

        foreach ($this->_aImageFields as $sField => $aParams) {
            $oStorage = BxDolStorage::getObjectInstance($aParams['storage_object']);
            $aFiles = $oStorage->getGhosts($iProfileId, $iContentId);

            foreach ($aFiles as $aFile) {
                if (!$oStorage->getFile($aFile['id']))
                    continue;
                $bRet = $oStorage->deleteFile($aFile['id'], $this->_iAccountProfileId);
            }
        }

        return parent::delete($iContentId, $aContentInfo);
    }

    protected function genCustomViewRowValueProfileEmail($aInput)
    {
        $CNF = &$this->_oModule->_oConfig->CNF;
        if(empty($aInput['value']))
            return '';

        $sEmail = $aInput['value'];

        $sModuleAccounts = 'bx_accounts';
    	if(!BxDolModuleQuery::getInstance()->isEnabledByName($sModuleAccounts))
    		return $sEmail;

		$oModuleAccounts = BxDolModule::getInstance($sModuleAccounts);
		if(!$oModuleAccounts || empty($oModuleAccounts->_oConfig->CNF['URL_MANAGE_ADMINISTRATION']))
			return $sEmail;

        return $this->_oModule->_oTemplate->parseHtmlByName('name_link.html', array(
            'href' => BX_DOL_URL_ROOT . BxDolPermalinks::getInstance()->permalink($oModuleAccounts->_oConfig->CNF['URL_MANAGE_ADMINISTRATION'], array(
            	'filter' => urlencode($sEmail)
            )),
            'title' => '',
            'content' => $sEmail
        ));
    }

    protected function genCustomViewRowValueProfileStatus($aInput)
    {
        $CNF = &$this->_oModule->_oConfig->CNF;
        if(empty($aInput['value']))
            return '';

        $sStatus = _t('_sys_profile_status_' . $aInput['value']);
        if(empty($CNF['URL_MANAGE_ADMINISTRATION']) || empty($CNF['FIELD_TITLE']) && !empty($this->aInputs[$CNF['FIELD_TITLE']]['value']))
            return $sStatus;

        return $this->_oModule->_oTemplate->parseHtmlByName('name_link.html', array(
            'href' => BX_DOL_URL_ROOT . BxDolPermalinks::getInstance()->permalink($CNF['URL_MANAGE_ADMINISTRATION'], array(
            	'filter' => urlencode($this->aInputs[$CNF['FIELD_TITLE']]['value'])
            )),
            'title' => '',
            'content' => $sStatus
        ));
    }

    protected function _associalFileWithContent($oStorage, $iFileId, $iProfileId, $iContentId, $sPictureField = '')
    {
        $oStorage->updateGhostsContentId ($iFileId, $iProfileId, $iContentId, $this->_isAdmin($iContentId));
        $this->_oModule->_oDb->updateContentPictureById($iContentId, 0/*$iProfileId*/, $iFileId, $sPictureField);
    }

    protected function _getProfilePhotoGhostTmplVars($sField, $aContentInfo = array())
    {
    	$CNF = &$this->_oModule->_oConfig->CNF;

    	return array (
			'name' => $this->aInputs[$sField]['name'],
            'content_id' => $this->aInputs[$sField]['content_id'],
			'bx_if:set_thumb' => array (
				'condition' => false,
				'content' => array (),
			),
		);
    }

    protected function _isAdmin ($iContentId = 0)
    {
        if (parent::_isAdmin ($iContentId))
            return true;
        if (!$iContentId || !($aDataEntry = $this->_oModule->_oDb->getContentInfoById((int)$iContentId)))
            return false;
        return CHECK_ACTION_RESULT_ALLOWED == $this->_oModule->checkAllowedEdit ($aDataEntry);        
    }
}

/** @} */
