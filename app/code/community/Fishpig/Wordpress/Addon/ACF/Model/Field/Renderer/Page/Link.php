<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Addon_ACF_Model_Field_Renderer_Page_Link extends Fishpig_Wordpress_Addon_ACF_Model_Field_Renderer_Abstract
{
	/**
	 * Render the value
	 *
	 * @return $this
	 */
	protected function _render()
	{
		if ($value = $this->getValue()) {
			$this->setValue(false);
			
			if (is_array($value)) {
				$posts = Mage::getResourceModel('wordpress/post_collection')
					->addIsViewableFilter()
					->setOrderByPostDate()
					->addFieldToFilter('main_table.ID', array('IN' => $value));
				
				$links = array();
				
				foreach($posts as $post)	{
					$links[] = $post->getUrl();
				}
				
				if (count($links) > 0) {
					$this->setValue($links);
				}
			}
			else {
				$post = Mage::getModel('wordpress/post')->load((int)$value);
			
				if ($post->getId()) {
					$this->setValue($post->getUrl());
				}
			}
		}
		
		return parent::_render();
	}
}
