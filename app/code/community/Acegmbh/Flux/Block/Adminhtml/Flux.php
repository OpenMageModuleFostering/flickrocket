<?php

class Acegmbh_Flux_Block_Adminhtml_Flux
	extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	/**
	 * Constructor.
	 * 
	 * @version 0.1
	 * 
	 * @param void
	 * 
	 * @return object
	 */
	public function __construct()
	{
		$this->_controller = 'adminhtml_flux';
		$this->_blockGroup = 'flux';
		$this->_headerText = Mage::helper('flux')->__('FlickRocket SOAP Log');
		parent::__construct();
		$this->removeButton('add');
    }
}
