<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Addon_ACF_Model_Field_Renderer_Wysiwyg extends Fishpig_Wordpress_Addon_ACF_Model_Field_Renderer_Abstract
{
    /**
     * Render the value
     *
     * @return $this
     */
    protected function _render()
    {
        if ($value = $this->getValue()) {
			$this->setValue(
				Mage::helper('wordpress/filter')->addParagraphsToString($value)
			);
		}

        return parent::_render();
    }
}

