<?php

class Acegmbh_Flux_Model_Orders
	extends Mage_Core_Model_Abstract
{
	/**
	 * Constructor.
	 * 
	 * @version 0.1
	 * 
	 * @param void
	 * 
	 * @return void
	 */
	public function _construct()
	{
		parent::_construct();
		$this->_init('flux/orders');
	}

        public function getLegacyItems()
   	{
	   $session = Mage::getSingleton('customer/session');
	   if(!$session->isLoggedIn())
	   return null;
            $purchased = Mage::getResourceModel('downloadable/link_purchased_collection')
            ->addFieldToFilter('customer_id', $session->getCustomerId())
            ->addOrder('created_at', 'desc');
        $this->setPurchased($purchased);
        $purchasedIds = array();
        foreach ($purchased as $_item) {
            $purchasedIds[] = $_item->getId();
        }
        if (empty($purchasedIds)) {
            $purchasedIds = array(null);
        }
        $purchasedItems = Mage::getResourceModel('downloadable/link_purchased_item_collection')
            ->addFieldToFilter('purchased_id', array('in' => $purchasedIds))
            ->addFieldToFilter('status',
                array(
                    'nin' => array(
                        Mage_Downloadable_Model_Link_Purchased_Item::LINK_STATUS_PENDING_PAYMENT,
                        Mage_Downloadable_Model_Link_Purchased_Item::LINK_STATUS_PAYMENT_REVIEW
                    )
                )
            )
            ->addFieldToFilter(array('link_url','link_url'), array(array('neq'=>'#'),array('null' => 'null')))
            //->addFieldToFilter('link_type', array('eq'=>'file'))
            
            ->setOrder('item_id', 'desc');
            //echo  $purchasedItems->getSelect();
        	return $purchasedItems;
         
	}
	
        public function getDigitalItems()
   	{
	   $session = Mage::getSingleton('customer/session');
	   if(!$session->isLoggedIn())
	   return null;
           $purchased = Mage::getResourceModel('downloadable/link_purchased_collection')
            ->addFieldToFilter('customer_id', $session->getCustomerId())
            ->addOrder('created_at', 'desc');
           $this->setPurchased($purchased);
           $purchasedIds = array();
	   foreach ($purchased as $_item) {
		    $purchasedIds[] = $_item->getId();
		}
		if (empty($purchasedIds)) {
		    $purchasedIds = array(null);
		}
		$purchasedItems = Mage::getResourceModel('downloadable/link_purchased_item_collection')
		    ->addFieldToFilter('purchased_id', array('in' => $purchasedIds))
		    ->addFieldToFilter('status',
		        array(
		            'nin' => array(
		                Mage_Downloadable_Model_Link_Purchased_Item::LINK_STATUS_PENDING_PAYMENT,
		                Mage_Downloadable_Model_Link_Purchased_Item::LINK_STATUS_PAYMENT_REVIEW
		            )
		        )
		    )
		    ->addFieldToFilter(array('link_url','link_url'), array(array('eq'=>'#'),array('notnull' => 'null')))		    
		    ->setOrder('item_id', 'desc');	
	   //echo  $purchasedItems->getSelect();
	   return $purchasedItems;
	}
	
	
	public function lastPurchasedItem()
	{
	   $session = Mage::getSingleton('customer/session');
	   if(!$session->isLoggedIn())
	   return null;
	   $purchased = Mage::getResourceModel('downloadable/link_purchased_collection')
            ->addFieldToFilter('customer_id', $session->getCustomerId())
            ->addOrder('created_at', 'desc');
           $this->setPurchased($purchased);
           $purchasedIds = array();
        foreach ($purchased as $_item) {
            $purchasedIds[] = $_item->getId();
        }
        if (empty($purchasedIds)) {
            $purchasedIds = array(null);
        }
        $purchasedItems = Mage::getResourceModel('downloadable/link_purchased_item_collection')
            ->addFieldToFilter('purchased_id', array('in' => $purchasedIds))
            ->addFieldToFilter('status',
                array(
                    'nin' => array(
                        Mage_Downloadable_Model_Link_Purchased_Item::LINK_STATUS_PENDING_PAYMENT,
                        Mage_Downloadable_Model_Link_Purchased_Item::LINK_STATUS_PAYMENT_REVIEW
                    )
                )
            )->getLastItem();
              
            return $purchasedItems;
	}
	
}
