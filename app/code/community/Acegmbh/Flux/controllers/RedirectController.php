<?php

class Acegmbh_Flux_RedirectController extends 
	Mage_Core_Controller_Front_Action
{
	public function indexAction()
	{	$request=$this->getRequest();
	
		$projectId=$request->getParam('project_id');
		
		$url=Mage::getUrl();
		if(!empty($projectId))
		{
		  $model=Mage::getModel('flux/product_redirect');
		  $url=$model->getRedirectUrl($projectId);
		}
		
		$this->_redirectUrl($url);
		return;
	}
	
	public function viewAction()
	{
		 $legacy=$this->getRequest()->getParam('legacy');
		 
		$cookie = Mage::getSingleton('core/cookie');
		if($legacy=='true')
		{ //Magento List
		  $cookie->set('downloadoption', '1' ,time()+86400,'/');
		}
		else
		{ //Flick Rocket
		  $cookie->set('downloadoption', '2' ,time()+86400,'/');
		}
		///////////////////////////////////////////////////////
		$this->_redirect('downloadable/customer/products/');
	}
}
