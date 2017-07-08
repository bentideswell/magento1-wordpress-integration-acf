<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Addon_ACF_Model_Resource_Field extends Fishpig_Wordpress_Model_Resource_Abstract
{
	/**
	 * Fields to be unserialized after load
	 *
	 * @var array
	 */
	protected $_serializableFields = array(
		'meta_value' => array(null, array()),
	);
	
	/**
	 * Initialise the model
	 *
	 */
	public function _construct()
	{
		$this->_init('wp_addon_acf/field', 'meta_key');
	}
	
	/**
	 * Custom load SQL to combine required tables
	 *
	 * @param string $field
	 * @param string|int $value
	 * @param Mage_Core_Model_Abstract $object
	 */
	protected function _getLoadSelect($field, $value, $object)
	{
		if ($object->getScope()) {
			$select = $this->_getScopedLoadSelect($field, $value, $object);
		}
		else if (Mage::helper('wp_addon_acf')->isProVersion()) {
			$select = $this->_getReadAdapter()
				->select()
					->distinct()
					->from(array('main_table' => $this->getTable('wordpress/post')), array(
						'field_post_id' => 'ID',
						'meta_key' => 'post_name', 
						'meta_value' => 'post_content',
					))
					->join(
						array('meta' => $this->getTable('wordpress/post_meta')),
						"meta.meta_value=main_table.post_name",
						''
					)
					->where('meta.meta_key=?', '_' . $value)
					->limit(1);
					
			if ($object->getPost()) {
				$select->where('meta.post_id = ?', $object->getPost()->getId());
			}
		}
		else {
			$select = $this->_getReadAdapter()
				->select()
					->distinct()
					->from(array('main_table' => $this->getMainTable()), '');
			
			if (is_array($value)) {
				$select->where('main_table.meta_key=?', '_' . $value[0]);
				$select->where('main_table.post_id=?', $value[1]);
			}
			else {
				$select->where('main_table.meta_key=?', '_' . $value);
			}
			
			$select->join(
				array('_table' => $this->getMainTable()),
				'`_table`.`meta_key`=`main_table`.`meta_value`',
				'*'
			);
		}

		return $select;
	}
	
	/**
	 * Custom load SQL to combine required tables
	 *
	 * @param string $field
	 * @param string|int $value
	 * @param Mage_Core_Model_Abstract $object
	 */
	protected function _getScopedLoadSelect($field, $value, $object)
	{
		if (Mage::helper('wp_addon_acf')->isProVersion()) {
			$select = $this->_getReadAdapter()->select()
				->from(array('main_table' => $this->getTable('wordpress/option')), '');
			
			if (is_array($value)) {
				$select->where('main_table.option_name=?', '_' . $object->getScope() . '_' . $value[0])
					->where('main_table.post_id=?', $value[1]);
			}
			else {
				$select->where('main_table.option_name IN (?)', array(
					'_' . $object->getScope() . '_' . $value,
					$object->getScope() . '_' . $value,
					$object->getScope() . '__' . $value, // Might be wrong, but leave it just incase
				));
			}
			
			$select->join(
				array('_table' => $this->getTable('wordpress/post')),
				'`_table`.`post_name`=`main_table`.`option_value`',
				array(
					'field_post_id' => 'ID',
					'meta_key' => 'post_name',
					'meta_value' => 'post_content',
				)
			)
			->where('_table.post_content <> ?', '');
		}
		else {
			$select = $this->_getReadAdapter()->select()
				->from(array('main_table' => $this->getTable('wordpress/option')), '');
			
			if (is_array($value)) {
				$select->where('main_table.option_name=?', '_' . $object->getScope() . '_' . $value[0])
					->where('main_table.post_id=?', $value[1]);
			}
			else {
				$select->where('main_table.option_name=?', '_' . $object->getScope() . '_' . $value);
			}
			
			$select->join(
				array('_table' => $this->getMainTable()),
				'`_table`.`meta_key`=`main_table`.`option_value`',
				'*'
			);
		}

		return $select->limit(1);
	}
	
	/**
	 * Get an array of subfields
	 *
	 * @param Fishpig_Wordpress_Model_Addon_ACF_Model_Field $field
	 * @return array
	 */
	public function getSubFields($field)
	{
		$select = $this->_getReadAdapter()
			->select()
				->from($this->getTable('wordpress/post'), array(
					'label' => 'post_title',
					'name' => 'post_excerpt',
					'key' => 'post_name',
					'post_content',
				))
				->where('post_parent=?', $field->getFieldPostId())
				->where('post_type=?', 'acf-field');

		if ($subFields = $this->_getReadAdapter()->fetchAll($select)) {
			foreach($subFields as $key => $subField) {
				$subFields[$key] = array_merge($subField, 
					unserialize($subField['post_content']),
					array('scope' => $field->getScope())
					
				);
			}

			return $subFields;
		}
		
		return false;
	}
	
	/**
	 * Migrate the now unserialized data in meta_key into the root of the
	 * data array
	 *
	 * @param Mage_Core_Model_Abstract $object
	 * @return $this
	 */
	protected function _afterLoad(Mage_Core_Model_Abstract $object)
	{
		parent::_afterLoad($object);
		
		$object->addData($object->getData('meta_value'))
			->unsetData('meta_value');

		return $this;
	}	
}
