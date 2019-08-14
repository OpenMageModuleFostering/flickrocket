<?php

class Acegmbh_Flux_Block_Adminhtml_Flux_Grid
	extends Mage_Adminhtml_Block_Widget_Grid
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
		parent::__construct();
		$this->setId('FluxOrderGrid');
		$this->setDefaultSort('flux_order_id');
		$this->setDefaultDir('DESC');
		$this->setSaveParametersInSession(true);
	}

	/**
	 * Prepare the collection of physical orders.
	 * 
	 * @version 0.1
	 * 
	 * @param void
	 * 
	 * @return object
	 */
	protected function _prepareCollection()
	{
		$collection = Mage::getModel('flux/orders')->getCollection();
		$this->setCollection($collection);
		return parent::_prepareCollection();
	}
	
	/**
	 * Prepare the columns.
	 * 
	 * @version 0.1
	 * 
	 * @param void
	 * 
	 * @return object
	 */
	protected function _prepareColumns()
	{
		$this->addColumn('flux_order_id', array(
											'header'	=> Mage::helper('flux')->__('ID'),
											'align'		=> 'right',
											'width'		=> '50px',
											'index'		=> 'flux_order_id'
										)
						);
		$this->addColumn('order_id', array(
											'header'	=> Mage::helper('flux')->__('Order ID'),
											'align'		=> 'right',
											'width'		=> '50px',
											'index'		=> 'order_id'
									)
						);
		$this->addColumn('store_id', array(
											'header'	=> Mage::helper('flux')->__('Store ID'),
											'align'		=> 'right',
											'width'		=> '50px',
											'index'		=> 'store_id'
									)
						);
		$this->addColumn('customer_id', array(
											'header'	=> Mage::helper('flux')->__('Customer ID'),
											'align'		=> 'right',
											'width'		=> '50px',
											'index'		=> 'customer_id'
									)
						);
		$this->addColumn('request', array(
											'header'	=> Mage::helper('flux')->__('Request'),
											'align'		=> 'right',
											'width'		=> '150px',
											'index'		=> 'request',
											'renderer'	=> 'Acegmbh_Flux_Block_Adminhtml_Flux_Grid_Render_Xml'
									)
						);
		$this->addColumn('response', array(
											'header'	=> Mage::helper('flux')->__('Response'),
											'align'		=> 'right',
											'width'		=> '150px',
											'index'		=> 'response',
											'renderer'	=> 'Acegmbh_Flux_Block_Adminhtml_Flux_Grid_Render_Xml'
									)
						);
		$this->addColumn('error', array(
											'header'	=> Mage::helper('flux')->__('Error'),
											'align'		=> 'right',
											'width'		=> '150px',
											'index'		=> 'error'
									)
						);
		$this->addColumn('date', array(
											'header'	=> Mage::helper('flux')->__('Order Date'),
											'align'		=> 'right',
											'width'		=> '150px',
											'index'		=> 'date',
											'type'		=> 'time'
									)
						);
		return parent::_prepareColumns();
	}
	
	/**
	 * Get Row URL.
	 * 
	 * @version 0.1
	 * 
	 * @param object $row
	 * 
	 * @return string
	 */
	public function getRowUrl($row)
	{
		return;
	}
}
