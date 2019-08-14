<?php

class Acegmbh_Flux_Block_Adminhtml_Admin
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
		$this->_controller = 'adminhtml_flickrocketadmin';
		$this->_blockGroup = 'admin';
		$this->_headerText = Mage::helper('flux')->__('FlickRocket Admin');
		parent::__construct();
		$this->removeButton('add');
    }
}
