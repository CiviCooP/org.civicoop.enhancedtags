<?php
/*
 * Copyright (C) 2014 Coöperatieve CiviCooP U.A. <http://www.civicoop.org>
 * Licensed under the AGPL-3.0
 */
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
        _enhancedtags_add_action($values);
        break;
      case CRM_Core_Action::UPDATE:
        $values['tag_id'] = $form->getVar('_id');
        $defaultValues = $form->getVar('_defaultValues');
        _enhancedtags_update_action($values, $defaultValues);
        break;
    }
  }
}
/**
 * Function to get current coordinator id from form array defaultValues
 * 
 * @param array $defaultValues
 * @return int $currentCoordinatorId
 */
function _enhancedtags_get_current_coordinator_id($defaultValues) {
  $currentCoordinatorId = 0;
  if (isset($defaultValues['coordinator_id']) && !empty($defaultValues['coordinator_id'])) {
    $currentCoordinatorId = $defaultValues['coordinator_id'];
  }
  return $currentCoordinatorId;
}
/**
 * Function to update enhanced tag
 * Possible scenarios:
 * 1 - completely new coordinator is added. In that case defaultValues will not
 *     hold coordinator_id and new one is to be created
 * 2 - data of exisiting coordinator updated. In that case coordinator is the same 
 *     as in defaultValues and existing data is to be updated
 * 3 - new coordinator is introduced. In that case coordinator_id in defaultValues
 *     is not empty but different. Old coordinator to be ended and new one to
 *     be created
 * 4 - new coordinator is 0. In that case old to be ended
 * 
 * @param array $newValues
 * @param array $defaultValues
 */
function _enhancedtags_update_action($newValues, $defaultValues) {
  if (!isset($defaultValues['coordinator_id'])) {
    if (isset($newValues['coordinator_id']) && !empty($newValues['coordinator_id'])) {
      _enhancedtags_add_tag_enhanced($newValues);
    }
  } else {
    if ($newValues['coordinator_id'] == $defaultValues['coordinator_id']) {
      _enhancedtags_update_tag_enhanced($newValues);
    } else {
      _enhancedtags_terminate_tag_enhanced($defaultValues, $newValues['coordinator_start_date']);
      if ($newValues['coordinator_id'] != 0) {
        _enhancedtags_add_tag_enhanced($newValues);
      }
    }
  }
}
/**
 * Function to terminate enhanced tag
 * 
 * @param array $values
 */
function _enhancedtags_terminate_tag_enhanced($values, $startDate) {
  if ($values['coordinator_id'] != 0) {
    $activeTag = CRM_Enhancedtags_BAO_TagEnhanced::getActiveByTagId($values['id']);
    if (!empty($activeTag)) {
      $params = array('id' => $activeTag['id'], 'is_active' => 0);
      if (!empty($startDate)) {
        $endDate = new DateTime($startDate);
      } else {
        $endDate = new DateTime();
      }
      $endDate->sub(new DateInterval('P1D'));
      $params['end_date'] = $endDate->format('Ymd');
      CRM_Enhancedtags_BAO_TagEnhanced::add($params);
    }
  }
}
/**
 * Function to get active enhanced tag for coordinator and update
 * 
 * @param array $values
 */
function _enhancedtags_update_tag_enhanced($values) {
  $currentTag = CRM_Enhancedtags_BAO_TagEnhanced::getActiveByTagId($values['tag_id']);
  if (!empty($currentTag)) {
    $params['id'] = $currentTag['id'];
    if (isset($values['coordinator_start_date'])) {
      $params['start_date'] = _enhancedtags_process_date($values['coordinator_start_date'], 'Ymd');
    }
    if (isset($values['coordinator_end_date'])) {
      $params['end_date'] = _enhancedtags_process_date($values['coordinator_end_date'], 'Ymd');
    }
    $now = new DateTime();
    if (!empty($params['end_date']) && $params['end_date'] < $now->format('Ymd')) {
      $params['is_active'] = 0;
    }
    CRM_Enhancedtags_BAO_TagEnhanced::add($params);
  }
}
/**
 * Function to format date
 * 
 * @param string $inDate
 * @return string $date->format
 * 
 */
function _enhancedtags_process_date($inDate, $format) {
  if (empty($inDate)) {
    return '';
  } else {
    $outDate = new DateTime($inDate);
    return $outDate->format($format);
  }
}
/**
 * Function to process add action for enhanced tag
 * 
 * @param array $values
 */
function _enhancedtags_add_action($values) {
  if (isset($values['coordinator_id']) && !empty($values['coordinator_id'])) {
    $tagQuery = 'SELECT MAX(id) as maxTagId FROM civicrm_tag';
    $tagDao = CRM_Core_DAO::executeQuery($tagQuery);
    if ($tagDao->fetch()) {
      $values['tag_id'] = $tagDao->maxTagId;
      _enhancedtags_add_tag_enhanced($values);
    }
  }
}
/**
 * Function to add enhanced tag
 * 
 * @param array $values
 */
function _enhancedtags_add_tag_enhanced($values) {
  $params = array('tag_id' => $values['tag_id'], 'coordinator_id' => $values['coordinator_id']);
  if (isset($values['coordinator_start_date'])) {
    if (empty($values['coordinator_start_date'])) {
      $params['start_date'] = '';
    } else {
      $startDate = new DateTime($values['coordinator_start_date']);
      $params['start_date'] = $startDate->format('Ymd');
    }
  }
  $params['is_active'] = 1;
  if (isset($values['coordinator_end_date'])) {
    if (empty($values['coordinator_end_date'])) {
      $params['end_date'] = '';
    } else {
      $endDate = new DateTime($values['coordinator_end_date']);
      $nowDate = new DateTime();
      if ($endDate < $nowDate) {
        $params['is_active'] = 0;
      }
      $params['end_date'] = $endDate->format('Ymd');
    }
  }
  CRM_Enhancedtags_BAO_TagEnhanced::add($params);
}
/**
 * Function to add coordinator data to tag admin form
 * 
 * @param object $form
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
 * 
 * @param object $form
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
      $enhancedTag = CRM_Enhancedtags_BAO_TagEnhanced::getActiveByTagId($tagId);
      if (isset($enhancedTag['start_date'])) {
        list($defaults['coordinator_start_date']) = CRM_Utils_Date::setDateDefaults($enhancedTag['start_date']);
      }
      if (isset($enhancedTag['end_date'])) {
        list($defaults['coordinator_end_date']) = CRM_Utils_Date::setDateDefaults($enhancedTag['end_date']);
      }
      if (isset($enhancedTag['coordinator_id'])) {
        $defaults['coordinator_id'] = $enhancedTag['coordinator_id'];
      }
      break;
  }
  if (!empty($defaults)) {
    $form->setDefaults($defaults);
  }
}
/**
 * Implementation of hook civicrm_pageRun
 * add coordinator to tag admin page
 * 
 * @param object $page
 */
function enhancedtags_civicrm_pageRun(&$page) {
  $pageName = $page->getVar('_name');
  if ($pageName == 'CRM_Admin_Page_Tag') { 
    /*
     * retrieve all active tag enhanced data and put in array with tag_id as index
     */
    $enhancedTags = CRM_Enhancedtags_BAO_TagEnhanced::getValues(array('is_active' => 1));
    $coordinators = array();
    foreach ($enhancedTags as $enhancedTag) {
      $coordinators[$enhancedTag['tag_id']] = CRM_Enhancedtags_BAO_TagEnhanced::getCoordinatorName($enhancedTag['coordinator_id']);
    }
    $page->assign('coordinators', $coordinators);
  }
}
/**
 * Implementation of hook civicrm_merge
 * When merging tags
 */
function enhancedtags_civicrm_merge($type, &$data, $mainId = NULL, $otherId = NULL, $tables = NULL ) {
  if ($tables[0] == 'civicrm_entity_tag' && $tables[1] = 'civicrm_tag') {
    CRM_Enhancedtags_BAO_TagEnhanced::merge($mainId, $otherId);
  }
}
/**
 * Implementation of hook civicrm_post
 * - when a tag is deleted, retrieve active enhanced and set end date to today
 *  and active is 0
 */
function enhancedtags_civicrm_post($op, $objectName, $objectId, &$objectRef) {
  if ($op === 'delete' && $objectName === 'Tag') {
    $activeTag = CRM_Enhancedtags_BAO_TagEnhanced::getActiveByTagId($objectId);
    $params = array('id' => $activeTag['id'], 'is_active' => 0);
    $endDate = new DateTime();
    if (empty($activeTag['end_date']) || $activeTag['end_date'] > $endDate->format('Y-m-d')) {
      $params['end_date'] = $endDate->format('Ymd');
    }
    CRM_Enhancedtags_BAO_TagEnhanced::add($params);
  }
}
/**
 * Implementation of hook civicrm_summary
 * Add coordinator data on summary if present
 */
function enhancedtags_civicrm_summary($contactID, &$content, &$contentPlacement) { 
  $displayData = array();
  $coordinatorData = CRM_Enhancedtags_BAO_TagEnhanced::getValues(array('coordinator_id' => $contactID));
  foreach ($coordinatorData as $coordinator) {
    $displayData[] = _enhancedtags_create_coordinator_text($coordinator);
  }
  $coordinator = implode(', ', $displayData);
  if (!empty($coordinator)) {
    $content = _enhancedtags_summary_coordinator_content($coordinator);
  }
}
/**
 * Function to set coordinator field on summary
 */
function _enhancedtags_summary_coordinator_content($coordinator) {
  $content = "<div id='coordinator'><div class='crm-summary-row'><div class='crm-label'>"
    . "Coordinator of </div><div class='crm-content'>".$coordinator.
    "</div></div></div>";
  $content .= <<< EOT
    <script>
    cj(function($){
      if ($(".crm-contact_type_label").length == 0) {
        CRM.alert("Someone has changed the summary layout, coordinator can't be displayed properly");
      return;
    }
    $(".crm-contact_type_label").parent().parent().prepend($("#coordinator").html());
  });
  </script>
EOT;

  return $content;
}
/**
 * Function to create text line for coordinator
 * 
 * @param type $coordinator
 * @return string $coordinatorTxt
 */
function _enhancedtags_create_coordinator_text($coordinator) {
  if (empty($coordinator)) {
    return '';
  }
  $txtParts = array();
  $txtParts[] = _enhancedtags_get_tag_name($coordinator['tag_id']);
  if (isset($coordinator['start_date']) && !empty($coordinator['start_date'])) {
    $txtParts[] = 'from '._enhancedtags_process_date($coordinator['start_date'], 'd-m-Y');
  }
  if (isset($coordinator['end_date']) && !empty($coordinator['end_date'])) {
    $txtParts[] = 'to '._enhancedtags_process_date($coordinator['end_date'], 'd-m-Y');
  }
  $coordinatorTxt = implode(' ', $txtParts);
  return $coordinatorTxt;
}
/**
 * Function to retrive name of a tag with API
 * 
 * @param type $tagId
 * @return string $tagName
 */
function _enhancedtags_get_tag_name($tagId) {
  try {
    $tagName = civicrm_api3('Tag', 'Getvalue', array('id' => $tagId, 'return' => 'name'));
    return $tagName;
  } catch (CiviCRM_API3_Exception $ex) {
    return '';
  }
}