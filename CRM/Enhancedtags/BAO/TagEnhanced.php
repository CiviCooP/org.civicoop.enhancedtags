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
  public static function getByTagId($tagId) {
    $tagEnhanced = new CRM_Enhancedtags_BAO_TagEnhanced();
    $tagEnhanced->tag_id = $tagId;
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
   * Function to update Enhanced tag
   * 
   * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
   * @date 3 Jun 2014
   * @param array $params 
   * @return array $result
   * @access public
   * @static
   */
  public static function updateByTagId($params) {
    if (empty($params)) {
      throw new Exception('Params can not be empty when updating an enhanced tag');
    }
    if (!isset($params['id']) && !isset($params['tag_id'])) {
      throw new Exception('Params have to contain id or tag_id when updating an enhanced tag');      
    }
    $tagEnhanced = new CRM_Enhancedtags_BAO_TagEnhanced();
    if (!isset($params['id'])) {
      $tagEnhanced->tag_id = $params['tag_id'];
      $tagEnhanced->find(true);
      $params['id'] = $tagEnhanced->id;
    }
    $result = self::add($params);
    return $result;
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
    $tagEnhanced->delete();
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