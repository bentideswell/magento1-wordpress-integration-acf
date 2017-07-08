<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Addon_ACF_Model_Field_Renderer_Relationship extends Fishpig_Wordpress_Addon_ACF_Model_Field_Renderer_Abstract
{
    /**
     * Render the value
     *
     * @return $this
     */
	protected function _render()
	{
		if ($value = $this->getValue()) {
			if ($this->getField()->getReturnFormat() === 'object') {
				$this->setValue(false);

				if (is_array($value)) {
					$posts = Mage::getResourceModel('wordpress/post_collection')
						->addPostTypeFilter('*')
						->addIsViewableFilter()
						->addFieldToFilter('ID', array('in' => $value));

					$this->setValue($posts);
				}
				else {
					$post = Mage::getModel('wordpress/post')->setPostType('*')->load($value);
					
					if ($post->getId()) {
						$this->setValue($post);
					}
				}
			}
		}

		return parent::_render();
	}
}
