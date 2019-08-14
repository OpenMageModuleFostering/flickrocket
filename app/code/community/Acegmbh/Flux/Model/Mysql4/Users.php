<?php
  
class Acegmbh_Flux_Model_Mysql4_Users
	extends Mage_Core_Model_Mysql4_Abstract
{
	/**
	 * Constructor.
	 * 
	 * @version 0.1
	 */
	public function _construct()
	{
		$this->_init('flux/users', 'flux_user_id');
	}
}