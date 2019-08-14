<?php
$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$setup->startSetup();
$setup->updateAttribute('catalog_product', 'license_id', 'apply_to', 'downloadable');
$setup->updateAttribute('catalog_product', 'project_id', 'apply_to', 'downloadable');
$setup->updateAttribute('catalog_product', 'license_id', 'is_global', '1');
$setup->updateAttribute('catalog_product', 'project_id', 'is_global', '1');
$setup->updateAttribute('catalog_product', 'license_id', 'is_configurable', '1');
$setup->updateAttribute('catalog_product', 'project_id', 'is_configurable', '1');

$entityTypeId=$setup-> getEntityTypeId('catalog_product');
$attributeSetId=$setup->getAttributeSetId($entityTypeId, 'FlickRocketProduct');

$groupId=$setup->getAttributeGroupId($entityTypeId, $attributeSetId, 'FlickRocket Attributes');
$attributeId = $setup->getAttributeId('catalog_product','links_purchased_separately');
$setup->addAttributeToGroup($entityTypeId, $attributeSetId, $groupId, $attributeId, null);

$setup->endSetup();

