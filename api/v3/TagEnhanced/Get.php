<?php

/**
 * TagEnhanced.Get API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 * @return void
 * @see http://wiki.civicrm.org/confluence/display/CRM/API+Architecture+Standards
 */
function _civicrm_api3_tag_enhanced_get_spec(&$spec) {
  $spec['id']['api.required'] = 0;
  $spec['tag_id']['api.required'] = 0;
  $spec['coordinator_id']['api.required'] = 0;
  $spec['start_date']['api.required'] = 0;
  $spec['end_date']['api.required'] = 0;
  $spec['is_active']['api.required'] = 0;
}

/**
 * TagEnhanced.Get API
 *
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */
function civicrm_api3_tag_enhanced_get($params) {
  $tagEnhanced = CRM_Enhancedtags_BAO_TagEnhanced::getValues($params);
  return civicrm_api3_create_success($tagEnhanced, $params, 'TagEnhanced', 'Get');
}

