<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Addon_ACF_Model_Field extends Fishpig_Wordpress_Model_Abstract
{
	/**
	 * Initialise the model
	 *
	 */
	public function _construct()
	{
		$this->_init('wp_addon_acf/field');
	}
	
	/**
	 * Get the return format of the field
	 * This adds legacy support for ACF Free
	 *
	 * @return string
	 */
	public function getReturnFormat()
	{
		return $this->hasReturnFormat()
			? $this->_getData('return_format')
			: $this->getSaveFormat();
	}

	/**
	 * Renders the initial meta value as it's ACF value
	 *
	 * @return false|mixed
	 */
	public function render()
	{
		$transportObject = new Varien_Object();
		
		Mage::dispatchEvent('wordpress_addon_acf_field_get_renderer', array('transport' => $transportObject, 'field' => $this, 'field_type' => $this->getType()));
		
		$renderer = $transportObject->getRenderer() ? $transportObject->getRenderer() : $this->_getRenderer($this->getType());

		if ($renderer) {
			if (!$renderer->getKey()) {
				$renderer->setKey($this->getKey());
			}
			
			if (!$renderer->getScope() && $this->getScope()) {
				$renderer->setScope($this->getScope());
			}
			
			return $renderer->setField($this)
				->setPost($this->getPost())
				->setValue($this->getValue())
				->render();
		}

		return false;
	}
	
	/**
	 * Retrieves the rendering class
	 * If class based on $type isn't found, returns default renderer
	 *
	 * @param string $type
	 * @return Fishpig_Wordpress_Addon_ACF_Model_Field_Renderer_Abstract
	 */
	protected function _getRenderer($type)
	{
		$types = array($type, 'default');
		$baseDir = Mage::getModuleDir('', 'Fishpig_Wordpress_Addon_ACF') . DS . 'Model' . DS . 'Field' . DS . 'Renderer' . DS;

		foreach($types as $type) {
			$classFile = $baseDir . uc_words($type, DS) . '.php';
			
			if (is_file($classFile) && ($renderer = Mage::getModel('wp_addon_acf/field_renderer_' . $type)) !== false) {
				return $renderer->setKey($this->getKey())->setScope($this->getScope());
			}
		}
		
		return false;
	}
	
	/**
	 * Get an array of sub-fields
	 *
	 * @return array
	 */
	public function getSubFields()
	{
		if (!$this->hasSubFields()) {
			$this->setSubFields(
				$this->getResource()->getSubFields($this)
			);
		}

		return $this->getData('sub_fields');
	}
}
