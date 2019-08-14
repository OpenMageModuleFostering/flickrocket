<?php
class Acegmbh_Flux_Model_Observer_Template
{

	public function setTemplate(Varien_Event_Observer $observer)
	{   
	    $template=Mage::getStoreConfig('acegmbh_flux/flux/flux_template');
	    
	    $layout = $observer->getEvent()->getLayout();
	    if($template=="1")
	    {
	      $update = $layout->getUpdate();
	      $update->addHandle('flickRocket_only');
            }
	    
	    if($template=="2")
	    {
		 $update = $layout->getUpdate();
		 $update->addHandle('flickRocket_and_legacy');
            }
	}
}
