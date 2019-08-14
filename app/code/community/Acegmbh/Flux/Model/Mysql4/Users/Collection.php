<?php

class Acegmbh_Flux_Model_Mysql4_Users_Collection
	extends Mage_Core_Model_Mysql4_Collection_Abstract
{
	/**
	 * Constructor.
	 * 
	 * @version 0.1
	 * 
	 * @param void
	 * 
	 * @return void
	 */
	public function _construct()
	{
		$this->_init('flux/users');
	}
}