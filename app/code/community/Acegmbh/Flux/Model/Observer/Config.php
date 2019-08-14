<?php
class Acegmbh_Flux_Model_Observer_Config
{   

    protected $_groups=array();
    
    
    
    public function validateAccountData($observer)
    {
		$object=$observer->getEvent()->getObject();
		

		$section = $object->getSection();
		if($section=='acegmbh_flux')
		{	$this->_groups=Mage::app()->getRequest()->getParam('groups');
			$eMail=$this->_getUsername();
			$password=$this->_getPassword();
			$url=$this->_getUrl();
			$themId=$this->_getThemeId();
			try
			{
			    $result=Mage::helper('flux')->checkAccount($url,$eMail, $password, $themId);
			    /*if($result!==true)
			    {
			    	throw new Exception('Invalid account data.');
			    }*/
			}
			catch(Exception $e)
			{
			
			  throw new Exception($e->getMessage());
			}
		}
			
   }

    protected function _getUsername()
    {
    	 if(isset($this->_groups['flux']['fields']['flux_email']['value']))
    	 {
    	 	return $this->_groups['flux']['fields']['flux_email']['value'];
    	 }
    	 return null;
    }
    
    protected function _getPassword()
    {
    	if(isset($this->_groups['flux']['fields']['flux_password']['value']))
    	 {
    	 	return $this->_groups['flux']['fields']['flux_password']['value'];
    	 }
    	return null;
    }
    
    protected function _getUrl()
    {
    	if(isset($this->_groups['flux']['fields']['flux_wsdl']['value']))
    	 {
    	 	return $this->_groups['flux']['fields']['flux_wsdl']['value'];
    	 }
    	return null;
    }
    
    protected function _getThemeId()
    {
    	if(isset($this->_groups['flux']['fields']['flux_theme_id']['value']))
    	 {
    	 	return $this->_groups['flux']['fields']['flux_theme_id']['value'];
    	 }
    	return null;
    }
    
}
