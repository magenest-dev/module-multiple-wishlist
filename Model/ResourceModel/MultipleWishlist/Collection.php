<?php

namespace Magenest\MultipleWishlist\Model\ResourceModel\MultipleWishlist;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected function _construct()
    {
        $this->_init('Magenest\MultipleWishlist\Model\MultipleWishlist', 'Magenest\MultipleWishlist\Model\ResourceModel\MultipleWishlist');
    }
}
