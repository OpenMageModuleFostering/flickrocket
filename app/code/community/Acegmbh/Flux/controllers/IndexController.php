<?php

class Acegmbh_Flux_IndexController extends 
	Mage_Adminhtml_Controller_Action
{
	public function indexAction()
	{
		$this->loadLayout();
		$this->renderLayout();
	}
}