<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Addon_ACF_Model_Field_Renderer_Repeater extends Fishpig_Wordpress_Addon_ACF_Model_Field_Renderer_Abstract
{
	/**
	 * Process all sub-fields and set result as array
	 *
	 * @return $this
	 */
	protected function _render()
	{
		$this->setValue(
			$this->_renderRepeaterSubFields($this->getField()->getSubFields(), $this->getKey() . '_')
		);

		return parent::_render();
	}	
}
