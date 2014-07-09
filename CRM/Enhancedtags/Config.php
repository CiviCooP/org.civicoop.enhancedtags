<?php
/**
 * Class following Singleton pattern for specific extension configuration
 *
 * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
 * @date 2 Jun 2014

 *  * Copyright (C) 2014 Coöperatieve CiviCooP U.A. <http://www.civicoop.org>
 * Licensed under the AGPL-3.0
 */
class CRM_Enhancedtags_Config {
  /*
   * singleton pattern
   */
  static private $_singleton = NULL;
  /*
   * coordinator list
   */
  public $coordinatorList = NULL;
  /**
   * Constructor function
   */
  function __construct() {
    $this->setCoordinatorList();
  }
  private function setCoordinatorList() {
    $params = array('contact_sub_type' => 'Expert', 'options' => array('limit' => 0));
    $apiContact = civicrm_api3('Contact', 'Get', $params);
    foreach($apiContact['values'] as $contact) {
      $this->coordinatorList[$contact['id']] = $contact['sort_name'];
    }
    asort($this->coordinatorList);
  }
  /**
   * Function to return singleton object
   * 
   * @return object $_singleton
   * @access public
   * @static
   */
  public static function &singleton() {
    if (self::$_singleton === NULL) {
      self::$_singleton = new CRM_Enhancedtags_Config();
    }
    return self::$_singleton;
  }
}
