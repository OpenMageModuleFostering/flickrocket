<?php

class Acegmbh_Flux_Model_Customer
    extends Mage_Customer_Model_Customer
{	

/*	
	public function setPassword($password)
	{
		$this->setData('password', $password);
		$strHash = $this->hashPassword($password, 'MD');
		$this->setPasswordHash($strHash);
		return $this;
	}
*/	
	/**
	 * Hash customer password
	 *
	 * @param   string $password
	 * @param   int    $salt
	 * @return  string
	 */
	public function hashPassword($password, $salt = null)
	{
		return Mage::helper('core')->getHash($password, 'MD');
	}
}