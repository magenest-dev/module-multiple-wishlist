<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Wishlist item option resource model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magenest\MultipleWishlist\Model\ResourceModel\Item;

class Option extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('magenest_multiplewishlist_item_option', 'option_id');
    }
}
