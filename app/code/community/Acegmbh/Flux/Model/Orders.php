<?php

class Acegmbh_Flux_Model_Orders
	extends Mage_Core_Model_Abstract
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
		parent::_construct();
		$this->_init('flux/orders');
	}
}