<?php
/**
 * BAO Tagenhanced for dealing with enhanced tags
 * 
 * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
 * @date 3 Jun 2014
 * 
 * Copyright (C) 2014 Coöperatieve CiviCooP U.A. <http://www.civicoop.org>
 * Licensed under the AGPL-3.0
 */
class CRM_Enhancedtags_BAO_TagEnhanced extends CRM_Enhancedtags_DAO_TagEnhanced {

  /**
   * Function to get values
   * 
   * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
   * @date 3 Jun 2014
   * @param array $params name/value pairs with field names/values
   * @return array $result found rows with data
   * @access public
   * @static
   */
  public static function getValues($params) {
    $result = array();
    $tagEnhanced = new CRM_Enhancedtags_BAO_TagEnhanced();
    if (!empty($params)) {
      $fields = self::fields();
      foreach ($params as $paramKey => $paramValue) {
        if (isset($fields[$paramKey])) {
          $tagEnhanced->$paramKey = $paramValue;
        }
      }
    }
    $tagEnhanced->find();
    while ($tagEnhanced->fetch()) {
      $row = array();
      self::storeValues($tagEnhanced, $row);
      $result[$row['id']] = $row;
    }
    return $result;
  }
  public static function getActiveByTagId($tagId) {
    $tagEnhanced = new CRM_Enhancedtags_BAO_TagEnhanced();
    $tagEnhanced->tag_id = $tagId;
    $tagEnhanced->is_active = 1;
    $tagEnhanced->find(true);
    $result = array();
    self::storeValues($tagEnhanced, $result);
    return $result;
  }
  /**
   * Function to add or update Enhanced tag
   * 
   * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
   * @date 3 Jun 2014
   * @param array $params 
   * @return array $result
   * @access public
   * @static
   */
  public static function add($params) {
    $result = array();
    if (empty($params)) {
      throw new Exception('Params can not be empty when adding an enhanced tag');
    }
    $tagEnhanced = new CRM_Enhancedtags_BAO_TagEnhanced();
    $fields = self::fields();
    foreach ($params as $paramKey => $paramValue) {
      if (isset($fields[$paramKey])) {
        $tagEnhanced->$paramKey = $paramValue;
      }
    }
    $tagEnhanced->save();
    self::storeValues($tagEnhanced, $result);
    return $result;
  }
  /**
   * Function to calculate end date for a coordinator when a new one is added
   * 
   * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
   * @date 27 Jun 2014
   * @param array $params
   * @param object $tagEnhanced
   * @return object $endDate
   * @access private
   * @static
   */
  private static function calculateCoordinatorEndDate($params, $tagEnhanced) {
    if (empty($tagEnhanced->end_date)) {
      if (isset($params['start_date'])) {
        $endDate = new DateTime($params['start_date']);
      } else {
        $endDate = new DateTime();
      }
      $endDate->sub(new DateInterval('P1D'));
    } else {
      $endDate = new DateTime($tagEnhanced->end_date);
    }
    return $endDate;
  }  
  /**
   * Function to set end date for active enhanced when tag is deleted
   * (specific for PUM because sector coordinator has to be remembered
   * even when sector (= tag) is removed)
   * 
   * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
   * @date 27 Jun 2014
   * @param int $tagId
   * @access public
   * @static
   */
  public static function processDeletedTag($tagId) {
    if (!empty($tagId)) {
      $tagEnhanced = new CRM_Enhancedtags_BAO_TagEnhanced();
      $tagEnhanced->tag_id = $tagId;
      $tagEnhanced->is_active = 1;
      $tagEnhanced->find();
      if ($tagEnhanced->fetch()) {
        $tagEnhanced->is_active = 0;
        $tagEnhanced->end_date = CRM_Utils_Date::processDate(date('Ymd'));
        $tagEnhanced->save();
      }
    }
  }
  /**
   * Function to delete Enhanced tag
   * 
   * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
   * @date 3 Jun 2014
   * @param int $tagId 
   * @return boolean
   * @access public
   * @static
   */
  public static function deleteByTagId($tagId) {
    if (empty($tagId)) {
      throw new Exception('Tag id can not be empty when attempting to delete an enhanced tag');
    }
    
    $tagEnhanced = new CRM_Enhancedtags_BAO_TagEnhanced();
    $tagEnhanced->tag_id = $tagId;
    $tagEnhanced->find();
    while ($tagEnhanced->fetch()) {
      $tagEnhanced->delete();
    }
    return TRUE;
  }
  /**
   * Function to get the name of the coordinator
   * 
   * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
   * @date 10 jun 2014
   * @param int $coordinatorId
   * @return string $coordinatorName
   * @access public
   * @static
   */
  public static function getCoordinatorName($coordinatorId) {
    try {
      $coordinatorName = civicrm_api3('Contact', 'Getvalue', array('id' => $coordinatorId, 'return' => 'display_name'));
    } catch (CiviCRM_API3_Exception $ex) {
      $coordinatorName = '';
    }
    return $coordinatorName;
  }
}