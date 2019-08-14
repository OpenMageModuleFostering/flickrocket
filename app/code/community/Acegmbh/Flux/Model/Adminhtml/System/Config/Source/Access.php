<?php
class Acegmbh_Flux_Model_Adminhtml_System_Config_Source_Access 
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
    	$arrReturn = array();
   	$arrReturn[] = array('value' => '1','label' => 'FlickRocket only');
   	
   	$arrReturn[] = array('value' => '2', 'label' => 'FlickRocket and legacy');

	return $arrReturn;
    }
}
