<?php
$Setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$Setup->startSetup();
$Setup->addAttributeSet(Mage_Catalog_Model_Product::ENTITY, 'FlickRocketProduct');
$iIdAttributeSet = $Setup->getAttributeSetId(Mage_Catalog_Model_Product::ENTITY, 'FlickRocketProduct');
$Setup->addAttribute('catalog_product', 'video_resolution',
			array(
		        	'group'	=> 'FlickRocket Attributes',
				'input'	=> 'select',
				'type'	=> 'int',
				'label'	=> 'Video Resolution',
				'backend' => '',
				'visible' => 1,
				'required' => '0',
				'source'   => 'acegmbh_flux/adminhtml_product_attribute_source_resolution',
				'user_defined'	=> 1,
				'searchable'	=> 0,
				'filterable'	=> 0,
				'comparable'	=> 0,
				'visible_on_front' => 0,
				'visible_in_advanced_search' => 0,
				'is_html_allowed_on_front' => 0,
				'note'	 => 'HD content can also be offered in SD quality',
				'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
				'apply_to' => 'downloadable',
				)
			);

$Setup->updateAttribute('catalog_product', 'video_resolution', 'apply_to', 'downloadable');
$attributeId = $Setup->getAttribute('catalog_product','video_resolution');
$Setup->addAttributeToSet('catalog_product',
			  $iIdAttributeSet,
			  'FlickRocket Attributes',
			$attributeId['attribute_id']);
$Setup->updateAttribute('catalog_product', 'project_id', 'source_model', NULL);
$Setup->updateAttribute('catalog_product', 'project_id', 'note', 'Identifies the product in FlickRocket');
$Setup->updateAttribute('catalog_product', 'license_id', 'note', 'Select a license defining the DRM restrictions');
//acegmbh_flux/adminhtml_system_config_source_projectids
//////
$Setup->addAttributeSet(Mage_Catalog_Model_Product::ENTITY, 'FlickRocketProduct');
$iIdAttributeSet = $Setup->getAttributeSetId(Mage_Catalog_Model_Product::ENTITY, 'FlickRocketProduct');
$Setup->addAttribute('catalog_product', 'project_wizard',
			array(
		        	'group'	=> 'FlickRocket Attributes',
				'input'	=> 'button',
				'type'	=> 'static',
				'label'	=> 'Project Wizard',
				'backend' => '',
				'visible' => 1,
				'required' => false,
				'user_defined'	=> 1,
				'searchable'	=> 0,
				'filterable'	=> 0,
				'comparable'	=> 0,
				'visible_on_front' => 0,
				'visible_in_advanced_search' => 0,
				'is_html_allowed_on_front' => 0,
				'note'	 => '',
				'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
				'apply_to' => 'downloadable',
				)
			);

$Setup->updateAttribute('catalog_product', 'project_wizard', 'apply_to', 'downloadable');
$attributeId = $Setup->getAttribute('catalog_product','project_wizard');
$Setup->addAttributeToSet('catalog_product',
			  $iIdAttributeSet,
			  'FlickRocket Attributes',
			$attributeId['attribute_id']);

///Remove From default Group

$iIdAttributeSet = $Setup->getAttributeSetId(Mage_Catalog_Model_Product::ENTITY, 'Default');
$Setup->removeAttributeGroup(Mage_Catalog_Model_Product::ENTITY, $iIdAttributeSet, 'FlickRocket Attributes');

$Setup->endSetup();
