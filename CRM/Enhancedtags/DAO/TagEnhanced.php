<?php
/**
 * DAO TagEnhanced for dealing with tags
 * 
 * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
 * @date 3 Jun 2014
 * 
 * Copyright (C) 2014 Coöperatieve CiviCooP U.A. <http://www.civicoop.org>
 * Licensed under the AGPL-3.0
 */
class CRM_Enhancedtags_DAO_TagEnhanced extends CRM_Core_DAO {
  
  /**
   * static instance to hold the field values
   *
   * @var array
   * @static
   */
  static $_fields = null;
  
  /**
   * empty definition for virtual function
   */
  static function getTableName() {
    return 'civicrm_tag_enhanced';
  }
  
  /**
   * returns all the column names of this table
   *
   * @access public
   * @return array
   */
  static function &fields()
  {
    if (!(self::$_fields)) {
      self::$_fields = array(
        'id' => array(
          'name' => 'id',
          'type' => CRM_Utils_Type::T_INT,
          'required' => true
        ) ,
        'tag_id' => array(
          'name' => 'tag_id',
          'type' => CRM_Utils_Type::T_INT
        ) ,
        'coordinator_id' => array(
          'name' => 'coordinator_id',
          'type' => CRM_Utils_Type::T_INT
        ),
        'start_date' => array(
          'name' => 'start_date',
          'type' => CRM_Utils_Type::T_DATE,
        ) ,
        'end_date' => array(
          'name' => 'end_date',
          'type' => CRM_Utils_Type::T_DATE,
        )
      );
    }
    return self::$_fields;
  }
  /**
   * Returns an array containing, for each field, the array key used for that
   * field in self::$_fields.
   *
   * @access public
   * @return array
   */
  static function &fieldKeys()
  {
    if (!(self::$_fieldKeys)) {
      self::$_fieldKeys = array(
        'id'             =>  'id',
        'tag_id'         =>  'tag_id',
        'coordinator_id' =>  'coordinator_id',
        'start_date'            =>  'start_date',
        'end_date'              =>  'end_date'
      );
    }
    return self::$_fieldKeys;
  }
  
  
}