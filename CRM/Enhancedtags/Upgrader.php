<?php

/**
 * Collection of upgrade steps
 */
class CRM_Enhancedtags_Upgrader extends CRM_Enhancedtags_Upgrader_Base {
  public function install() {
    $this->executeSqlFile('sql/createTagEnhanced.sql');
  }
}
