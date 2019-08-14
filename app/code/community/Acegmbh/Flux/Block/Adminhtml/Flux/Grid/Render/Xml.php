<?php

class Acegmbh_Flux_Block_Adminhtml_Flux_Grid_Render_Xml
	extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	/**
	 * Render.
	 * 
	 * @version 0.1
	 * 
	 * @param object $Row
	 * 
	 * @return string
	 */
	public function render(Varien_Object $Row)
	{
		$value = $Row->getData($this->getColumn()->getIndex());
		$Dom = new DOMDocument;
		$Dom->preserveWhiteSpace = FALSE;
		$Dom->formatOutput = TRUE;
		$Dom->loadXml($value);
		return '<div style="width:450px; overflow:auto;"><textarea rows="20" cols="80" style="border:none; font-size:11px;">' . $Dom->saveXml() . '</textarea></div>';
	}
}