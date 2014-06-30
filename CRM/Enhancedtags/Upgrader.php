<?php

/**
 * Collection of upgrade steps

 *  * Copyright (C) 2014 Coöperatieve CiviCooP U.A. <http://www.civicoop.org>
 * Licensed under the AGPL-3.0
 */
class CRM_Enhancedtags_Upgrader extends CRM_Enhancedtags_Upgrader_Base {
  public function install() {
    $this->executeSqlFile('sql/createTagEnhanced.sql');
  }
}
