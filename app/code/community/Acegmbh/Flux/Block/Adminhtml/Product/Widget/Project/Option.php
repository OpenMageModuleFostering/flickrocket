<?php
class Acegmbh_Flux_Block_Adminhtml_Product_Widget_Project_Option
    extends Mage_Adminhtml_Block_Widget
    implements Varien_Data_Form_Element_Renderer_Interface {
    
    public function __construct()
    {
        $this->setTemplate('flux/product/widget/project/option.phtml'); //set a template
    }
    
    public function getProjects()
    {
      
      return Mage::helper('flux')->getProjects(); ;
    
    }
    
    
    public function render(Varien_Data_Form_Element_Abstract $element) {
        $this->setElement($element);
        return $this->toHtml();
    }
}
