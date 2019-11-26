<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magenest\MultipleWishlist\Model\ResourceModel\Item\Option;

use Magento\Catalog\Model\Product;
use Magenest\MultipleWishlist\Model\Item;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected function _construct()
    {
        $this->_init('Magenest\MultipleWishlist\Model\Item\Option', 'Magenest\MultipleWishlist\Model\ResourceModel\Item\Option');
    }
}
