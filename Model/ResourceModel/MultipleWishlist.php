<?php

namespace Magenest\MultipleWishlist\Model\ResourceModel;

class MultipleWishlist extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Store customer ID field name
     *
     * @var string
     */
    protected $_customerIdFieldName = 'customer_id';

    protected function _construct()
    {
        $this->_init('magenest_multiplewishlist_wishlist', 'id');
    }

    /**
     * Getter for customer ID field name
     *
     * @return string
     */
    public function getCustomerIdFieldName()
    {
        return $this->_customerIdFieldName;
    }

    /**
     * Setter for customer ID field name
     *
     * @param string $fieldName
     * @return $this
     */
    public function setCustomerIdFieldName($fieldName)
    {
        $this->_customerIdFieldName = $fieldName;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function save(\Magento\Framework\Model\AbstractModel $object)
    {
        $object->setHasDataChanges(true);
        return parent::save($object);
    }
}
