<?php
  
class Acegmbh_Flux_Model_Mysql4_Orders
	extends Mage_Core_Model_Mysql4_Abstract
{
	/**
	 * Constructor.
	 * 
	 * @version 0.1
	 */
	public function _construct()
	{
		$this->_init('flux/orders', 'flux_order_id');
	}
}