<?php
class Acegmbh_Flux_Model_Adminhtml_System_Config_Source_Themes extends Mage_Core_Model_Abstract
{
  
    public function toOptionArray()	
    {
    	$themes = Mage::helper('flux')->getThemes();
    	
    	$options = array();
    	$options[] = array('value' =>'','label' => Mage::helper('flux')->__('Please select'));	
    	if(count($themes)==1)
    	{
    		$options[] = array('value' => $themes->ID,'label' => $themes->Name);
    		return $options;
    	}
    	if(count($themes)>1)
    	{
	    foreach( $themes as $theme )
	    {
	    	$options[] = array('value' => $theme->ID,'label' => $theme->Name);       		    		
	    }
    	}

	return $options;
    }
    
    
    public function getOptionText($value)
    {	
    	$arr = Mage::helper('flux')->getThemes();
    	foreach( $arr as $theme )
    	{
    		if($theme->ID==$value)
    		{
    			return $theme->Name;
    		}
    	}
    }
    
    
}
