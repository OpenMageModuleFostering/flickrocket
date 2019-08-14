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
   		$arrReturn[] = array(	'value' => 'http://sandbox.flickrocket.com/services/OnDemandOrder/Service.asmx?WSDL',
   								'label' => 'Sandbox');
   		$arrReturn[] = array(	'value' => 'http://www.flickrocket.com/services/OnDemandOrder/Service.asmx?WSDL', 
   								'label' => 'Live');

		return $arrReturn;
    }
}