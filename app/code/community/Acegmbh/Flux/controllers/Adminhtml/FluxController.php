<?php

class Acegmbh_Flux_Adminhtml_FluxController
	extends Mage_Adminhtml_Controller_Action
{
	/**
	 * Initialisation action.
	 * 
	 * @version 0.1
	 * 
	 * @param void
	 * 
	 * @return object
	 */
	protected function _initAction()
	{
		$this->loadLayout()
				->_setActiveMenu('sales/flux')
				->_addBreadcrumb(Mage::helper('flux')->__('FlickRocket SOAP Log'), Mage::helper('flux')->__('FlickRocket SOAP Log'));
		return $this;
	}
	
	/**
	 * Index action.
	 * 
	 * @version 0.1
	 * 
	 * @param void
	 * 
	 * @return void
	 */
	public function indexAction()
	{
		$this->_initAction();
		$this->renderLayout();
	}

	/**
	 * Edit action.
	 * 
	 * @version 0.1
	 * 
	 * @param void
	 * 
	 * @return void
	 */
	public function editAction()
	{

	}
	
	/**
	 * New action.
	 * 
	 * @version 0.1
	 * 
	 * @param void
	 * 
	 * @return void
	 */
	public function newAction()
	{

	}
	
	/**
	 * Save action.
	 * 
	 * @version 0.1
	 * 
	 * @param void
	 * 
	 * @return void
	 */
	public function saveAction()
	{

	}
	
	/**
	 * Delete action.
	 * 
	 * @version 0.1
	 * 
	 * @param void
	 * 
	 * @return void
	 */
	public function deleteAction()
	{

	}

	/**
	 * Grid action.
	 * 
	 * @version 0.1
	 * 
	 * @param void
	 * 
	 * @return void
	 */
	public function gridAction()
	{
		$this->loadLayout();
		$this->getResponse()->setBody(
						$this->getLayout()->createBlock('importedit/adminhtml_flux_grid')->toHtml()
		);
	}
}