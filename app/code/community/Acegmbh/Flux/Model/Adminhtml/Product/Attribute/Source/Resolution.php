<?php
class Acegmbh_Flux_Model_Adminhtml_Product_Attribute_Source_Resolution 
		extends Mage_Core_Model_Abstract
{
    /**
     * Options getter
     *
     * @return array
     */
    public function getAllOptions()	
    {    	    	
    	$arrReturn = array();
    

	$arrReturn[] = array('value' => '0','label' => Mage::helper('flux')->__('SD Quality'));    		
    	$arrReturn[] = array('value' => '1','label' => Mage::helper('flux')->__('HD Quality'));

	return $arrReturn;
    }
    
    
    public function getOptionText($value)
    {
    	$arr = self::getAllOptions();
    	foreach( $arr as $key=>$val )
    	{
    		if($key==$value)
    		{
    			return $val;
    		}
    	}
    }
    
}
