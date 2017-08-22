<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Addon_ACF_Helper_Data extends Fishpig_Wordpress_Helper_Abstract
{
	/**
	 *
	 * @const string
	 */
	const PLUGIN_FILE_PRO = 'advanced-custom-fields-pro/acf.php';
	const PLUGIN_FILE_FREE = 'advanced-custom-fields/acf.php';
	
	/**
	 * Determine whether the plugin is enabled in WordPress
	 *
	 * @return string
	 */
	public function isEnabled()
	{
		$pluginHelper = Mage::helper('wordpress/plugin');
		
		return $pluginHelper->isEnabled(self::PLUGIN_FILE_PRO) || $pluginHelper->isEnabled(self::PLUGIN_FILE_FREE);
	}

	/**
	 *
	 *
	 */
	public function getField($key, $scope = null)
	{
		$value = null;

		try {
			// Get the core helper
			$coreHelper = Mage::helper('wp_addon_acf/core');
			
			// start the simulation
			$coreHelper->startWordPressSimulation();

			if (function_exists('get_field')) {
				$value = get_field($key, $scope);
			}
		}
		catch (Exception $e) {
			Mage::helper('wordpress')->log($e->getMessage());
		}
		
		// End the WordPress simulation
		$coreHelper->endWordPressSimulation();
		
		return $this->_fixFieldReturn($value);
	}

	/**
	 *
	 *
	 */
	public function getPostValueObserver(Varien_Event_Observer $observer)
	{
		$observer->getEvent()->getMeta()->setValue(
			$this->getField(
				$observer->getEvent()->getMeta()->getKey()
			)
		);
		
		return $this;	
	}

	/**
	 *
	 *
	 */
	public function getTermValueObserver(Varien_Event_Observer $observer)
	{
		$term = $observer->getEvent()->getObject();
		$meta = $observer->getEvent()->getMeta();

		$meta->setValue(
			$this->getField(
				$meta->getKey(),
				$term->getTaxonomy() . '_' . $term->getId()
			)
		);
		
		return $this;
	}
	
	/**
	 *
	 *
	 */
	protected function _fixFieldReturn($value)
	{
		if ($value) {
			if (is_array($value)) {
				foreach($value as $k => $v) {
					$value[$k] = $this->_fixFieldReturn($v);
				}
			}
			else if (is_object($value)) {
				$class = get_class($value);
				
				if ($class === 'WP_Post') {
					$value[$k] = $value = Mage::getModel('wordpress/post')->load($value->ID)->setWpPostObject($value);
				}
				else {
//					exit('Class not transposed: ' . get_class($value));
				}
			}
		}

		return $value;
	}
	
	/**
	 * @deprecated 2.0.0.0
	 */
	public function getOptionValue($key)
	{
		return $this->getField($key, 'options');
	}
	
	/**
	 * @deprecated 2.0.0.0
	 */
	public function getTermValue($key, Fishpig_Wordpress_Model_Term $term)
	{
		return $this->getField($key, $term->getTaxonomy() . '_' . $term->getId());
	}
	
	/**
	 * @deprecated 2.0.0.0
	 */
	public function getCategoryValue($key, $term)
	{
		return $this->getTermValue($key, $term);
	}
	
	/**
	 * @deprecated 2.0.0.0
	 */
	public function getValue($key, $scope)
	{
		return $this->getField($key, $scope);
	}
}
