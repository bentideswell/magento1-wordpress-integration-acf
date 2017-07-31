<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Addon_ACF_Model_Field_Renderer_Post_Object extends Fishpig_Wordpress_Addon_ACF_Model_Field_Renderer_Abstract
{
	/**
	 * Render the value
	 *
	 * @return $this
	 */
	protected function _render()
	{
		$value = $this->getValue();

		if ($value && !is_object($value)) {			
			$this->setValue(false);

			if (is_array($value)) {
				$posts = Mage::getResourceModel('wordpress/post_collection')
					->addPostTypeFilter('*')
					->addIsViewableFilter()
					->addFieldToFilter('main_table.ID', array('IN' => $value));

				$this->setValue($posts);
			}
			else if ('id' === $this->getField()->getReturnFormat()) {
				$this->setValue((int)$value);
			}
			else {
				$post = Mage::getModel('wordpress/post')->setPostType('*')->load((int)$value);
			
				if ($post->getId()) {
					$this->setValue($post);
				}
			}
		}
		
		return parent::_render();
	}
}
