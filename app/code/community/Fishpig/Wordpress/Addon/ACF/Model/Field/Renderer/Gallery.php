<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Addon_ACF_Model_Field_Renderer_Gallery extends Fishpig_Wordpress_Addon_ACF_Model_Field_Renderer_Abstract
{
    /**
     * Process all sub-fields and set result as array
     *
     * @return $this
     */
    protected function _render()
    {
        // Build the gallery from the selected IDs
        $gallery = array();

        foreach ((array)unserialize($this->getField()->getValue()) as $imageId) {
            if ($image = Mage::getModel('wordpress/image')->load($imageId)) {
                if ($image->getId()) {
                    $gallery[] = $image;
                }
            }
        }

        $this->setValue($gallery);

        return parent::_render();
    }
}
