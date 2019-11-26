<?php

namespace Magenest\MultipleWishlist\Model\ResourceModel;

class Item extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    protected function _construct()
    {
        $this->_init('magenest_multiplewishlist_item', 'id');
    }

}
