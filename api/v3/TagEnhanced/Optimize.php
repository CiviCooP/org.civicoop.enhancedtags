<?php
/**
 * TagEnhanced.Optimize API
 * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
 * Scheduled job removes records from civicrm_tag_enhanced where end_date is earlier
 * than start_date. These can be generated when switching coordinators
 *
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 * 
 * Copyright (C) 2014 Coöperatieve CiviCooP U.A. <http://www.civicoop.org>
 * Licensed under the AGPL-3.0
 */
function civicrm_api3_tag_enhanced_optimize($params) {
  $tagEnhanced = new CRM_Enhancedtags_BAO_TagEnhanced();
  $tagEnhanced->whereAdd('end_date < start_date');
  $tagEnhanced->find();
  while ($tagEnhanced->fetch()) {
    $tagEnhanced->delete();
  }
  $returnValues = array('is_error' => 0, 'message' => 'All enhanced tags with end_date earlier than start_date removed');
  return civicrm_api3_create_success($returnValues, $params, 'TagEnhanced', 'Optimize');
}

