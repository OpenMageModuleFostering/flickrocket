<?php
class Acegmbh_Flux_Model_Adminhtml_System_Config_Source_Wsdlurls 
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
    	$arrReturn = array();
   		$arrReturn[] = array(	'value' => 'https://sandbox.flickrocket.com/services/OnDemandOrder/Service.asmx?WSDL',
   								'label' => 'Sandbox');
   		$arrReturn[] = array(	'value' => 'https://www.flickrocket.com/services/OnDemandOrder/Service.asmx?WSDL', 
   								'label' => 'Live');

		return $arrReturn;
    }
}