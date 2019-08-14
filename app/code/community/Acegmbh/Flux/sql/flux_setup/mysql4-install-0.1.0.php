<?php
$Installer = $this;
$Setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$Setup->startSetup();
 
 /**
  * add attribute set 'FlickRocketProduct' and get its Id
  */
$Setup->addAttributeSet(Mage_Catalog_Model_Product::ENTITY, 'FlickRocketProduct');
$iIdAttributeSet = $Setup->getAttributeSetId(Mage_Catalog_Model_Product::ENTITY, 'FlickRocketProduct');



/**
 * get id of 'Default' attribute set
 */
$attributeSetIdDefault = $Setup->getAttributeSetId(Mage_Catalog_Model_Product::ENTITY, 'Default');
// echo '--->'.$attributeSetId.'<---';

/**
 * get groups of 'Default' attribute set
*/
$attributes = array();
$groups = Mage::getModel('eav/entity_attribute_group')
						->getResourceCollection()
						->setAttributeSetFilter($attributeSetIdDefault)
						->setSortOrder()
						->load();
foreach ($groups as $node)
{
	/*
	 [attribute_group_id] => 7
	[attribute_set_id] => 4
	[attribute_group_name] => General
	[sort_order] => 1
	[default_id] => 1
	*/
	// 	echo '***'.$node->getAttributeGroupId().'***';
	// 	echo '***'.$node->getAttributeSetId().'***';
	// 	echo '***'.$node->getAttributeGroupName().'***';
	// 	echo '***'.$node->getSortOrder().'***';
	// 	echo '***'.$node->getDefaultId().'***';

	/**
	 * add attribute group with same name
	 */
	$Setup->addAttributeGroup(	'catalog_product',
									'FlickRocketProduct',
									$node->getAttributeGroupName(),
									$node->getSortOrder());
	$attributeGroupId = $Setup->getAttributeGroup(	'catalog_product',
														'FlickRocketProduct',
														$node->getAttributeGroupName());
	$attributeGroupId = $attributeGroupId['attribute_group_id'];

	$iAttributeGroupId = $node->getId();
	$nodeChildren = Mage::getResourceModel('catalog/product_attribute_collection')
											->setAttributeGroupFilter($iAttributeGroupId)
											//->addFieldToFilter('is_user_defined', true) # I was trying to get user defined attributes.
											->addVisibleFilter()
											->load();
	if ($nodeChildren->getSize() > 0)
	{
		foreach ($nodeChildren->getItems() as $child)
		{
			// $child->getAttributeCode() );
			$Setup->addAttributeToGroup(Mage_Catalog_Model_Product::ENTITY,
											$iIdAttributeSet,
											$attributeGroupId,
											$child->getAttributeId(),
											$child->getSortOrder());

		}
	}
}


// /**
//  * get attribute group id of 'Default' attribute set of 'FlickRocketProduct' attribute set
//  */
// $attributes = array();
// $groups = Mage::getModel('eav/entity_attribute_group')
// 			->getResourceCollection()
// 			->setAttributeSetFilter($iIdAttributeSet)
// 			->setSortOrder()
// 			->load();
// foreach ($groups as $node)
// {
// 	//  preserve the attribute group id for later
// 	if( $node->getAttributeGroupName()=='General' )
// 	{
// 		$iIdAttributeGroup = $node->getAttributeGroupId();
// 	}
// 	if( $node->getAttributeGroupName()=='FlickRocket Attributes' )
// 	{
// 		$iIdAttributeGroupFlickRocket = $node->getAttributeGroupId();
// 	}
// }

//  /**
//   * get all attributes from 'Default' attribute set
//   */
// $attributeSetId = $Setup->getAttributeSetId(Mage_Catalog_Model_Product::ENTITY, 'Default');
// $attributes = array();
// $groups = Mage::getModel('eav/entity_attribute_group')
// 				->getResourceCollection()
// 				->setAttributeSetFilter($attributeSetId)
// 				->setSortOrder()
// 				->load();
// foreach ($groups as $node) 
// {
// 	$nodeChildren = Mage::getResourceModel('catalog/product_attribute_collection')
// 		->setAttributeGroupFilter($node->getId())
// 	//->addFieldToFilter('is_user_defined', true) # I was trying to get user defined attributes.
// 		->addVisibleFilter()
// 		->load();
// 	if ($nodeChildren->getSize() > 0) 
// 	{
// 		foreach ($nodeChildren->getItems() as $child) 
// 		{
// 			$attr = array(
// 					'id'                => $child->getAttributeId(),
// 					'text'              => $child->getAttributeCode()
// 			);
// 			$attributes[] = $attr;
			
// 			$Setup->addAttributeToGroup(Mage_Catalog_Model_Product::ENTITY,
// 										$iIdAttributeSet,
// 										$node->getId(),
// 										$child->getAttributeId());			
// 		}
// 	}
// }

/**
 * add all 'Default' attributes to 'Default' attribute group of 'FlickRocketProduct' attribute set 
 */
// foreach( $attributes as $attri )
// {
// 	$iIdAttribute = $attri['id'];
// 	$Setup->addAttributeToGroup(Mage_Catalog_Model_Product::ENTITY, 
// 								$iIdAttributeSet, 
// 								$iIdAttributeGroup, 
// 								$iIdAttribute);
// }

/**
 * add attribute set group (to sort your attributes into)
 */
$Setup->addAttributeGroup('catalog_product', 'FlickRocketProduct', 'FlickRocket Attributes', 1000);

/**
 * add project_id attribute to attribute-set "FlickRocketProduct" 
 */
$Setup->addAttribute('catalog_product', 'project_id',
						array(
								'group'							=> 'FlickRocket Attributes',
								'input'							=> 'select',
								'type'							=> 'int',
								'label'							=> 'Project ID',
								'backend'						=> '',
								'visible'						=> 1,
								'required'						=> 1,
								'user_defined'					=> 1,
								'searchable'					=> 0,
								'filterable'					=> 0,
								'comparable'					=> 0,
								'visible_on_front'				=> 0,
								'visible_in_advanced_search'	=> 0,
								'is_html_allowed_on_front'		=> 0,
								'source'                     	=> 'acegmbh_flux/adminhtml_system_config_source_projectids',
								'note'							=> 'Identifies the project in the FlickRocket',
								'global'						=> Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
								'apply_to'						=> 'downloadable',
						)
					);
$Setup->updateAttribute('catalog_product', 'project_id', 'apply_to', 'downloadable');
$attributeId = $Setup->getAttribute('catalog_product','project_id');
// echo '****'.$iIdAttributeSet.'****';
// echo '****'.$iIdAttributeGroupFlickRocket.'****';
// echo '****'.$attributeId.'****';
// print_r($attributeId);
// die('666');
$Setup->addAttributeToSet(	'catalog_product',
							$iIdAttributeSet,
							'FlickRocket Attributes',
							$attributeId['attribute_id']);
/**
 * add license_id attribute to attribute-set "FlickRocketProduct"
 */
$Setup->addAttribute('catalog_product', 'license_id',
						array(
								'group'							=> 'FlickRocket Attributes',
								'input'							=> 'select',
								'type'							=> 'int',
								'label'							=> 'License ID',
								'backend'						=> '',
								'visible'						=> 1,
								'required'						=> 1,
								'user_defined'					=> 1,
								'searchable'					=> 0,
								'filterable'					=> 0,
								'comparable'					=> 0,
								'visible_on_front'				=> 0,
								'visible_in_advanced_search'	=> 0,
								'is_html_allowed_on_front'		=> 0,
								'source'                     	=> 'acegmbh_flux/adminhtml_system_config_source_licenceids',
								'note'							=> 'Select a license defining the DRM restrictions (e.g. 19 for permantent download/up to three devices)', 
								'global'						=> Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
								'apply_to'						=> 'downloadable',
						)
					);
$Setup->updateAttribute('catalog_product', 'license_id', 'apply_to', 'downloadable');
$attributeId = $Setup->getAttribute('catalog_product','license_id');
$Setup->addAttributeToSet(	'catalog_product',
							$iIdAttributeSet,
							'FlickRocket Attributes',
							$attributeId['attribute_id']);

$Setup->run("
	DROP TABLE IF EXISTS `{$this->getTable('flux_orders')}`;
	CREATE TABLE `{$this->getTable('flux_orders')}` (
		`flux_order_id` int(11) unsigned NOT NULL auto_increment,
		`order_id` int(11) unsigned NOT NULL,
		`store_id` int(11) unsigned NOT NULL,
		`customer_id` int(11) unsigned NOT NULL,
		`request` text DEFAULT NULL,
		`response` text DEFAULT NULL,
		`error` varchar(100) DEFAULT NULL,
		`date` timestamp DEFAULT CURRENT_TIMESTAMP,
		PRIMARY KEY (`flux_order_id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$Setup->endSetup();
