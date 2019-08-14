<?php

class Acegmbh_Flux_Model_Observer_Order
{	
	const LOGFILE = 'Acegmbh_Flux_Model_Observer_Order.log';
	
	public function _log( $strLogMessage )
	{
		Mage::log($strLogMessage, null, self::LOGFILE );
	}


	public function beforeAdminOrder(Varien_Event_Observer $observer)
	{
		self::_log('beforeAdminOrder');
		$postData = Mage::app()->getRequest()->getPost();
		
		$customerId=isset($postData['customer_id'])?$postData['customer_id']:null;
		
	     if(!empty($customerId))
	     {
		$customer=Mage::getModel('customer/customer')->load($customerId);
		$passwordHash=$customer->getPasswordHash();
		$segments=explode(':',$passwordHash);
		$passwordMatched=true;
		if(!isset($segments[1]) || (isset($segments[1]) && $segments[1]!='MD'))
		{
			$passwordMatched=false;
		}
		
		
		if(isset($postData['item']) && !$passwordMatched)
		{
		   $item=$postData['item'];
		   foreach($item as $productId=>$val)
		   {
		       $product = Mage::getModel('catalog/product')->load($productId);
		       
		       if($product->getTypeId()=='downloadable') 
		       {
		       	  $projectId=$product->getProjectId();
		       	  $licenseId=$product->getLicenseId();
			  if(!empty($projectId) || !empty($licenseId))
			  {	
			       	   throw new Exception('User\'s password does not match.');
			  }
		       }
		       
		       if($product->getTypeId()=='configurable') 
		       {
		       	  $configurableAttributeCollection=$product->getTypeInstance()->getConfigurableAttributes(); 
		         foreach($configurableAttributeCollection as $attribute)
		         {  
		            $code=$attribute->getProductAttribute()->getAttributeCode();
		       	    
		       	    if($code=='project_id' || $code=='license_id')
		       	    {
		       	    	throw new Exception('User\'s password does not match.');
		       	    }
		       	  
			 }			
		       }
		      
		      if($product->getTypeId()=='bundle') 
		       {
		       	 foreach($val['bundle_option'] as $option=>$selected)
		       	 {
		       	  $selection=Mage::getModel('bundle/selection')->load($selected);
		       	  $childProduct=Mage::getModel('catalog/product')->load($selection->getProductId());
		       	  $projectId=$childProduct->getProjectId();
		       	  $licenseId=$childProduct->getLicenseId();
			  if(!empty($projectId) || !empty($licenseId))
			  {	
			       throw new Exception('User\'s password does not match.');

			  }
		       	 }
		       	  			
		       }
		       
		   }
		 
		}
	    }
	} 
}
