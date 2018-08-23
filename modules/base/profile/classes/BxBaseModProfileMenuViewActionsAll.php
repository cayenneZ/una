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
 * View entry all actions menu
 */
class BxBaseModProfileMenuViewActionsAll extends BxBaseModGeneralMenuViewActions
{
    public function __construct($aObject, $oTemplate = false)
    {
        parent::__construct($aObject, $oTemplate);
    }

    protected function _getMenuItemProfileFriendAdd($aItem)
    {
        return $this->_getMenuItemByNameActions($aItem);
    }

    protected function _getMenuItemProfileFriendRemove($aItem)
    {
        return $this->_getMenuItemByNameActionsMore($aItem);
    }

    protected function _getMenuItemProfileSubscribeAdd($aItem)
    {
        return $this->_getMenuItemByNameActions($aItem);
    }

    protected function _getMenuItemProfileSubscribeRemove($aItem)
    {
        return $this->_getMenuItemByNameActionsMore($aItem);
    }

    protected function _getMenuItemProfileSetAclLevel($aItem)
    {
        return $this->_getMenuItemByNameActions($aItem);
    }
}

/** @} */
