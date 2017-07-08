<?php
/**
 * @category Fishpig
 * @package Fishpig_Wordpress
 * @license http://fishpig.co.uk/license.txt
 * @author Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Addon_ACF_Model_Observer extends Fishpig_Wordpress_Addon_ACF_Model_Observer_Plugin_Abstract
{
	const PLUGIN_FILE_PRO = 'advanced-custom-fields-pro/acf.php';
	const PLUGIN_FILE_FREE = 'advanced-custom-fields/acf.php';
		
	/**
	 * Retrieve the module alias
	 *
	 * @return string
	 */
	protected function _getModuleAlias()
	{
		return 'wp_addon_acf';
	}
	
	/**
	 * Retrieve the module alias
	 *
	 * @return string
	 */
	protected function _getPluginFile()
	{
		if (Mage::helper('wordpress/plugin')->isEnabled(self::PLUGIN_FILE_PRO)) {
			return self::PLUGIN_FILE_PRO;
		}
		
		return self::PLUGIN_FILE_FREE;
	}
}
