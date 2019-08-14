<?php
class Acegmbh_Flux_Model_Adminhtml_System_Config_Source_Projectids 
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
    	$arr = Mage::helper('flux')->getProjects();
    	    	
    	$arrReturn = array();
    	foreach( $arr as $project )
    	{
    		$arrReturn[] = array(	'value' => $project->LongProjectID,
    								'label' => $project->Name);   

/*    		
    		<LongProjectID>0000-5517-51C6-E730</LongProjectID>
    		<Name>Big Buck Bunny</Name>
    		<CreationDate>2013-10-05T12:27:50</CreationDate>
    		<ContentType>fluxDVD</ContentType>
    		<Complete>true</Complete>
*/    		    		
    	}

//    		$arrReturn[] = array(	'value' => 241,
//    								'label' => '241 - another Licence ID');
		return $arrReturn;
    }
}