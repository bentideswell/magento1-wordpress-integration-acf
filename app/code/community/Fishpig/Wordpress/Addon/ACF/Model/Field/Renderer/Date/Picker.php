<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */
 
 /**
  * File provided by Matt Gifford.
  * Thanks Matt!
  */
class Fishpig_Wordpress_Addon_ACF_Model_Field_Renderer_Date_Picker extends Fishpig_Wordpress_Addon_ACF_Model_Field_Renderer_Abstract
{
	/**
	 * Render the value
	 *
	 * @return $this
	 */
	protected function _render()
	{
        if ($value = $this->getValue()) {
			$dateString = $this->setValue(substr($value, 0, 4) . '-' . substr($value, 4, 2) . '-' . substr($value, 6, 2));
        }

		return parent::_render();
	}
}
