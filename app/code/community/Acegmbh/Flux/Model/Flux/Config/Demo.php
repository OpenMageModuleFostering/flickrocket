<?php
class Acegmbh_Flux_Model_Flux_Config_Demo extends  Mage_Core_Model_Config_Data
{
    public function save()
    {	$username = Mage::getSingleton('admin/session')->getUser()->getUsername();
    	if($username=="demo"){
 		Mage::throwException('This is a demo version. You cannot change the values.'); 
 		return;
 	}
        return parent::save();
    }
}
