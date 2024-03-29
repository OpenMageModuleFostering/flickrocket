<?php
class Acegmbh_Flux_Model_Adminhtml_System_Config_Source_Licenceids 
		extends Mage_Core_Model_Abstract
{
    /**
     * Options getter
     *
     * @return array
     */
	static public function getAllOptions()	
    //public function toOptionArray()
    {
/*    	
    	return array(
    			'19'    => Mage::helper('flux')->__('19 - permanent download/up to three devices'),
    			'241'   => Mage::helper('catalog')->__('241 - another Licence ID')
    	);
    	*/
    	$arr = Mage::helper('flux')->getLicenses();
    	    	
    	$arrReturn = array();
    	foreach( $arr as $license )
    	{
    		$arrReturn[] = array(	'value' => $license->ID,
    								'label' => $license->Name);    		
    	}

//    		$arrReturn[] = array(	'value' => 241,
//    								'label' => '241 - another Licence ID');
		return $arrReturn;
    }
    
    
    public function getOptionText($value)
    {
    	$arr = Mage::helper('flux')->getLicenses();
    	foreach( $arr as $license )
    	{
    		if($license->ID==$value)
    		{
    			return $license->Name;
    		}
    	}
    }
    
}
