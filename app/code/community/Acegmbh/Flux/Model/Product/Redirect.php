<?php
class Acegmbh_Flux_Model_Product_Redirect extends Mage_Core_Model_Abstract
{
     
     protected function _getProductCollectionByProjectId($projectId)
     {
     	$collection=$this->_getCollection();
     	
     	$collection->addFieldToFilter('project_id',array('eq' => $projectId));
     	
     	$collection->addAttributeToSort('created_at', 'DESC');
	
	$collection->load();
    	
    	return $collection;
     	
     }
     
     
     protected function _getConfigurableProduct($projectId)
     {
     	$model=Mage::getModel('catalog/resource_product_type_configurable');
     	$childrenCollection=$this->_getProductCollectionByProjectId($projectId);
     	if($childrenCollection->count()==0)
     	  return false;
     	$childProductIds=array();
     	foreach($childrenCollection as $child);
     	{
     	   	$childProductIds[]=$child->getId();
     	}
     	$parentIds=$model->getParentIdsByChild($childProductIds);
     	if(count($parentIds)==0)
     	 return false;
     	$collection=$this->_getCollection();
     	$collection->addFieldToFilter('entity_id',array('IN' => $parentIds));
     	$collection->addAttributeToSort('created_at', 'DESC');
     	
     	$collection->addFieldToFilter('status',array('eq' => '1'));
     	$collection->addFieldToFilter('visibility',array('neq' => '1'));

     	return $collection->getFirstItem(); 
     }
     
     
     protected function _getBundleProduct($projectId)
     {
     	$model=Mage::getModel('bundle/product_type');
     	$childrenCollection=$this->_getProductCollectionByProjectId($projectId);
     	if($childrenCollection->count()==0)
     	  return false;
     	$childProductIds=array();
     	foreach($childrenCollection as $child);
     	{
     	   	$childProductIds[]=$child->getId();
     	}
     	$parentIds=$model->getParentIdsByChild($childProductIds);
     	if(count($parentIds)==0)
     	 return false;
     	$collection=$this->_getCollection();
     	$collection->addFieldToFilter('entity_id',array('IN' => $parentIds));
     	$collection->addAttributeToSort('created_at', 'DESC');
     	$collection->addFieldToFilter('status',array('eq' => '1'));
     	$collection->addFieldToFilter('visibility',array('neq' => '1'));
     	return $collection->getFirstItem();
     
     }
     
     protected function _getDownloadableProduct($projectId)
     {
     	$collection=$this->_getProductCollectionByProjectId($projectId);
     	
     	$collection->addFieldToFilter('status',array('eq' => '1'));
     	
     	$collection->addFieldToFilter('visibility',array('neq' => '1'));
     	if($collection->count()==0)
     	  return false;
     	
     	$product=$collection->getFirstItem();
     	$product=Mage::getModel('catalog/product')->load($product->getId());
     	if($product->getStatus()=="1" && $product->getvisibility()!="1")
     	  return $product;
     }
     
     public function getRedirectUrl($projectId)
     {	 
     	 $product=$this->_getConfigurableProduct($projectId);
     	 
     	 if($product instanceof Mage_Catalog_Model_Product)
     	 {
		$product =Mage::getModel('catalog/product')->load($product->getId());
		return $product->getProductUrl();
     	 }
     
     	$product=$this->_getBundleProduct($projectId);
     	 
     	 if($product instanceof Mage_Catalog_Model_Product)
     	 {
		$product =Mage::getModel('catalog/product')->load($product->getId());
		return $product->getProductUrl();
     	 }
     	 
     	 $product=$this->_getDownloadableProduct($projectId);
     	 
     	 if($product instanceof Mage_Catalog_Model_Product)
     	 {
		//$product =Mage::getModel('catalog/product')->load($product->getId());
		return $product->getProductUrl();
     	 }
     	 
     	 return Mage::getUrl();
     }
     
     protected function _getCollection()
     {
     	$collection=Mage::getModel('catalog/product')->getCollection();
     	return $collection;
     }
}
