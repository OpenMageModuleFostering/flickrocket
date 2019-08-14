<?php
class Acegmbh_Flux_Block_Adminhtml_Product_Widget_Project_Wizard_Button
    extends Mage_Adminhtml_Block_Widget
    implements Varien_Data_Form_Element_Renderer_Interface {
    
    public function __construct()
    {
        $this->setTemplate('flux/product/widget/project/wizard/button.phtml'); //set a template
    }
    
    public function render(Varien_Data_Form_Element_Abstract $element) {
        $this->setElement($element);
        return $this->toHtml();
    }
}
