<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Addon_ACF_Model_Field_Renderer_Flexible_Content extends Fishpig_Wordpress_Addon_ACF_Model_Field_Renderer_Abstract
{
	/**
	 * Process the flexible content field
	 *
	 * @return $this
	 */
	protected function _render()
	{
		$newValue = array();
		
		if (Mage::helper('wp_addon_acf')->isProVersion()) {
			$subFields = $this->getField()->getSubFields();

			if (is_array($subFields) && count($subFields) > 0) {
				if (($layouts = $this->_getFieldLayouts($this->getField())) !== false) {
					$blueprints = @unserialize($this->getField()->getValue());
	
					foreach($blueprints as $it => $layout) {
						if (!isset($layouts[$layout])) {
							continue;
						}
		
						$data = array(
							'acf_fc_layout' => $layout,
						);

						foreach($subFields as $subField) {
							if ($subField['parent_layout'] === $layouts[$layout]['key']) {
								if ('options' === $this->getScope()) {
									$data[$subField['name']] = Mage::helper('wp_addon_acf')->getOptionValue(
										$this->getField()->getOriginalKey() . $this->getKey() . '_' . $it . '_' . $subField['name']
									);
								}
								else {
									$data[$subField['name']] = $this->getPost()->getMetaValue(
										$this->getKey() . '_' . $it . '_' . $subField['name']
									);
								}
							}
						}
						
						$blueprints[$it] = $data;
					}
					
					$newValue = $blueprints;
				}
			}
		}
		else {
			if (($layouts = $this->_getFieldLayouts($this->getField())) !== false) {
				$blueprints = @unserialize($this->getField()->getValue());

				foreach($blueprints as $it => $layout) {
					if (!isset($layouts[$layout])) {
						continue;
					}
	
					$data = array(
						'acf_fc_layout' => $layout,
					);
					
					if (isset($layouts[$layout]['sub_fields'])) {
						foreach($layouts[$layout]['sub_fields']as $subField) {
							if ($subField['parent_layout'] === $layouts[$layout]['key']) {
								if ('options' === $this->getScope()) {
									$data[$subField['name']] = Mage::helper('wp_addon_acf')->getOptionValue(
										$this->getField()->getOriginalKey() . $this->getKey() . '_' . $it . '_' . $subField['name']
									);
								}
								else {
									$data[$subField['name']] = $this->getPost()->getMetaValue(
										$this->getKey() . '_' . $it . '_' . $subField['name']
									);
								}
							}
						}
						
						$blueprints[$it] = $data;
					}
				}
				
				$newValue = $blueprints;
			}
		}
		
		$this->setValue($newValue);
		
		return parent::_render();
	}
	
	/**
	 * Get a field's layout value in an easier to work with format
	 *
	 * @param
	 * @return false|array
	 */
	protected function _getFieldLayouts($field)
	{
		if (!$field->getLayouts()) {
			return false;
		}

		$layouts = array();
		
		foreach($field->getLayouts() as $layout) {
			$layouts[$layout['name']] = $layout;
		}
		
		return $layouts;
	}
	
}
