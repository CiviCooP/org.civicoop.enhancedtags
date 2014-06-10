<?php

require_once 'enhancedtags.civix.php';

/**
 * Implementation of hook_civicrm_config
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function enhancedtags_civicrm_config(&$config) {
  _enhancedtags_civix_civicrm_config($config);
}

/**
 * Implementation of hook_civicrm_xmlMenu
 *
 * @param $files array(string)
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function enhancedtags_civicrm_xmlMenu(&$files) {
  _enhancedtags_civix_civicrm_xmlMenu($files);
}

/**
 * Implementation of hook_civicrm_install
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function enhancedtags_civicrm_install() {
  return _enhancedtags_civix_civicrm_install();
}

/**
 * Implementation of hook_civicrm_uninstall
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function enhancedtags_civicrm_uninstall() {
  return _enhancedtags_civix_civicrm_uninstall();
}

/**
 * Implementation of hook_civicrm_enable
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function enhancedtags_civicrm_enable() {
  return _enhancedtags_civix_civicrm_enable();
}

/**
 * Implementation of hook_civicrm_disable
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function enhancedtags_civicrm_disable() {
  return _enhancedtags_civix_civicrm_disable();
}

/**
 * Implementation of hook_civicrm_upgrade
 *
 * @param $op string, the type of operation being performed; 'check' or 'enqueue'
 * @param $queue CRM_Queue_Queue, (for 'enqueue') the modifiable list of pending up upgrade tasks
 *
 * @return mixed  based on op. for 'check', returns array(boolean) (TRUE if upgrades are pending)
 *                for 'enqueue', returns void
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function enhancedtags_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _enhancedtags_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implementation of hook_civicrm_managed
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function enhancedtags_civicrm_managed(&$entities) {
  return _enhancedtags_civix_civicrm_managed($entities);
}

/**
 * Implementation of hook_civicrm_caseTypes
 *
 * Generate a list of case-types
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function enhancedtags_civicrm_caseTypes(&$caseTypes) {
  _enhancedtags_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implementation of hook_civicrm_alterSettingsFolders
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function enhancedtags_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _enhancedtags_civix_civicrm_alterSettingsFolders($metaDataFolders);
}
/**
 * Implementation of hook_civicrm_buildForm
 * - add elements to Tag Form for coordinator, start and end_date
 * 
 * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
 */
function enhancedtags_civicrm_buildForm($formName, &$form) {
  /*
   * if form is Tag Admin
   */
  if ($formName == 'CRM_Admin_Form_Tag') {
    _enhancedtags_add_coordinator_tag($form);
  }
}
/**
 * Implementation of hook_civicrm_postProcess
 * - create record in civicrm_tag_enhanced
 */
function enhancedtags_civicrm_postProcess($formName, &$form) {
  if ($formName == 'CRM_Admin_Form_Tag') {
    $action = $form->getVar('_action');
    $values = $form->exportValues();
    switch ($action) {
      case CRM_Core_Action::ADD:
        _enhancedtags_create_tag_enhanced($values);
        break;
      case CRM_Core_Action::UPDATE:
        $values['tag_id'] = $form->getVar('_id');
        _enhancedtags_update_tag_enhanced($values);
        break;
    }
  }
}
/**
 * Function to update enhanced tag
 */
function _enhancedtags_update_tag_enhanced($values) {
  $params = array();
  $params['tag_id'] = $values['tag_id'];
  if (isset($values['coordinator_id'])) {
    $params['coordinator_id'] = $values['coordinator_id'];
  }
  if (isset($values['coordinator_start_date'])) {
    $params['start_date'] = CRM_Utils_Date::processDate($values['coordinator_start_date']);
  }
  if (isset($values['coordinator_end_date'])) {
    $params['end_date'] = CRM_Utils_Date::processDate($values['coordinator_end_date']);
  }
  CRM_Enhancedtags_BAO_TagEnhanced::updateByTagId($params);
}
/**
 * Function to create enhanced tag
 */
function _enhancedtags_create_tag_enhanced($values) {
  $tagQuery = 'SELECT MAX(id) as tagId FROM civicrm_tag';
  $tagDao = CRM_Core_DAO::executeQuery($tagQuery);
  if ($tagDao->fetch()) {
    $params = array();
    $params['tag_id'] = $tagDao->tagId;
    if (isset($values['coordinator_id'])) {
      $params['coordinator_id'] = $values['coordinator_id'];
    }
    if (isset($values['coordinator_start_date'])) {
      $params['start_date'] = CRM_Utils_Date::processDate($values['coordinator_start_date']);
    }
    if (isset($values['coordinator_end_date'])) {
      $params['end_date'] = CRM_Utils_Date::processDate($values['coordinator_end_date']);
    }
    CRM_Enhancedtags_BAO_TagEnhanced::add($params);
  }
}
/**
 * Function to add coordinator data to tag admin form
 */
function _enhancedtags_add_coordinator_tag(&$form) {
  $enhancedTagsConfig = CRM_Enhancedtags_Config::singleton();
  $coordinatorList = $enhancedTagsConfig->coordinatorList;
  $coordinatorList[0] = '- select - ';
  asort($coordinatorList);
  $form->addElement('select', 'coordinator_id', ts('Coordinator'), $coordinatorList);
  $form->addDate('coordinator_start_date', ts('Start Date'), false);
  $form->addDate('coordinator_end_date', ts('End Date'), false);
  _enhancedtags_default_coordinator_tag($form);
}
/**
 * Function to set default coordinator data for update
 */
function _enhancedtags_default_coordinator_tag(&$form) {
  $defaults = array();
  $action = $form->getVar('_action');
  switch ($action) {
    case CRM_Core_Action::ADD:
      list($defaults['coordinator_start_date']) = CRM_Utils_Date::setDateDefaults(date('Ymd'));
      break;
    case CRM_Core_Action::UPDATE:
      $tagId = $form->getVar('_id');
      $enhancedTag = CRM_Enhancedtags_BAO_TagEnhanced::getByTagId($tagId);
      list($defaults['coordinator_start_date']) = CRM_Utils_Date::setDateDefaults($enhancedTag['start_date']);
      list($defaults['coordinator_end_date']) = CRM_Utils_Date::setDateDefaults($enhancedTag['end_date']);
      $defaults['coordinator_id'] = $enhancedTag['coordinator_id'];
      break;
  }
  if (!empty($defaults)) {
    $form->setDefaults($defaults);
  }
}
/**
 * Implementation of hook civicrm_pageRun
 * add coordinator to tag admin page
 */
function enhancedtags_civicrm_pageRun(&$page) {
  $pageName = $page->getVar('_name');
  if ($pageName == 'CRM_Admin_Page_Tag') { 
    /*
     * retrieve all tag enhanced data and put in array with tag_id as index
     */
    $enhancedTags = CRM_Enhancedtags_BAO_TagEnhanced::getValues(array());
    $coordinators = array();
    foreach ($enhancedTags as $enhancedTag) {
      $coordinators[$enhancedTag['tag_id']] = CRM_Enhancedtags_BAO_TagEnhanced::getCoordinatorName($enhancedTag['coordinator_id']);
    }
    $page->assign('coordinators', $coordinators);
  }
}
/**
 * Implementation of hook civicrm_merge
 * set end date for coordinator when merging tags
 * 
 * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
 * @date 10 Jun 2014
 */
function enhancedtags_civicrm_merge($type, &$data, $mainId = NULL, $otherId = NULL, $tables = NULL ) {
  if ($tables[0] == 'civicrm_entity_tag' && $tables[1] = 'civicrm_tag') {
    $params['tag_id'] = $mainId;
    $params['end_date'] = CRM_Utils_Date::processDate(date('Ymd'));
    CRM_Enhancedtags_BAO_TagEnhanced::updateByTagId($params);
  }
}
/**
 * Implementation of hook civicrm_post
 * set end date for coordinator when deleting tags
 * 
 * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
 * @date 10 Jun 2014
 */
function enhancedtags_civicrm_post($op, $objectName, $objectId, &$objectRef) {
  if ($op === 'delete' && $objectName === 'Tag') {
    $params['tag_id'] = $objectId;
    $params['end_date'] = CRM_Utils_Date::processDate(date('Ymd'));
    CRM_Enhancedtags_BAO_TagEnhanced::updateByTagId($params);
  }
}
