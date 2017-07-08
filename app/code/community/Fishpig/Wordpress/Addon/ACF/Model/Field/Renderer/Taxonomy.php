<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Addon_ACF_Model_Field_Renderer_Taxonomy extends Fishpig_Wordpress_Addon_ACF_Model_Field_Renderer_Abstract
{
    /**
     * Render the value
     *
     * @return $this
     */
	protected function _render()
	{
		if ($value = $this->getValue()) {
			if ($this->getField()->getReturnFormat() == 'object') {
				$model = 'wordpress/term';
				$taxonomy = $this->getField()->getTaxonomy();

				if ($taxonomy === 'category') {
					$model = 'wordpress/post_category';
				}
				else if ($taxonomy === 'post_tag') {
					$model = 'wordpress/post_tag';
				}
				
				$terms = array();

				foreach($value as $termId) {
					$term = Mage::getModel($model)->setTaxonomy($taxonomy)->load($termId);
					
					if ($term->getId()) {
						$terms[] = $term;
					}
				}
				
				if (count($terms) > 0) {
					$this->setValue($terms);
				}
			}
		}
		
		return parent::_render();
	}
}
