<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Addon_ACF_Model_Field_Renderer_Image extends Fishpig_Wordpress_Addon_ACF_Model_Field_Renderer_Abstract
{
	/**
	 * Render the value
	 *
	 * @return $this
	 */
	protected function _render()
	{
		if ($value = (int)$this->getValue()) {
			$image = Mage::getModel('wordpress/image')->load($value);

			if ($image->getId()) {
				if ($this->getField()->getReturnFormat() === 'object') {
					$this->setValue($image);
				}
				else if ($this->getField()->getReturnFormat() === 'url') {
					$this->setValue($image->getFullSizeImage());
				}
				else if ($this->getField()->getReturnFormat() === 'array') {
					$this->setValue(array(
						'id' => $image->getId(),
						'object' => $image,
						'url' => $image->getFullSizeImage(),
					));
				}
			}
		}
		
		return parent::_render();
	}
}
