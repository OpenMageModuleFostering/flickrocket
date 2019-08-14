<?php
class Acegmbh_Flux_Model_Observer_Template
{

	public function setTemplate(Varien_Event_Observer $observer)
	{   $request=Mage::app()->getRequest();
		
	    $action=$request->getActionName();
	    $controller=$request->getControllerName();
	    $module=$request->getModuleName();
	     
	    if($module=="downloadable" && $controller=="customer" && $action=="products")
	    { 	   $templateSet=false;
		   $content=$request->getParam("content");
		   $layout = $observer->getEvent()->getLayout();
		   if(empty($content))
		   {
		    
		    
		     $orders = Mage::getSingleton('flux/orders');
	 	     $lastItemPurchased=$orders->lastPurchasedItem();
	 	     if(empty($lastItemPurchased))
	 	      return ;
	 	 
		    if($lastItemPurchased->link_url=='#')
		    {
		      $update = $layout->getUpdate();
		      $update->addHandle('flickRocket_only');
		      $templateSet=true;
		    }
		    
		    if($lastItemPurchased->link_url!='#')
		    {	
			 $update = $layout->getUpdate();
			 $update->addHandle('flickRocket_and_legacy');
			 $templateSet=true;
		    }
		  }
		  
		  if($content=="2")
		  {	
		  	$update = $layout->getUpdate();
			$update->addHandle('flickRocket_only');
			$templateSet=true;
		  
		  }
		  
		  if($content=="1" || !$templateSet)
		  {   
		  	$update = $layout->getUpdate();
			$update->addHandle('flickRocket_and_legacy');
		  
		  }
		  
           }
	}
	
	
	public function convertWizardButton($observer) 
	{	
        	$form = $observer->getEvent()->getForm();
        	$projectWizard = $form->getElement('project_wizard');
        	if ($projectWizard) {
            	$projectWizard->setRenderer(
                	Mage::app()->getLayout()->createBlock('flux/adminhtml_product_widget_project_wizard_button')
            	 ); //
        	}
    	}
	
	
	public function projectOption($observer) 
	{	
        	$form = $observer->getEvent()->getForm();
        	$projectId= $form->getElement('project_id');
        	if ($projectId) {
            	$projectId->setRenderer(
                	Mage::app()->getLayout()->createBlock('flux/adminhtml_product_widget_project_option')->setForm($form)
            	 ); //
        	}
    	}
    	
    	public function addJavascriptBlock($observer)
    	{
    		$controller = $observer->getAction();
    		if("adminhtml_catalog_product_edit"!=$controller->getFullActionName() &&
    		   "adminhtml_catalog_product_new"!=$controller->getFullActionName() )
    		   return;
        	$layout = $controller->getLayout();
        	$block = $layout->createBlock('adminhtml/template');
        	$block->setTemplate('flux/product/widget/project/js.phtml');       
        	$layout->getBlock('js')->append($block);
    	}
	
}
