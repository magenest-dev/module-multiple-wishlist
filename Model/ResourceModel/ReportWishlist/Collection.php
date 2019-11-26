<?php
namespace Magenest\MultipleWishlist\Model\ResourceModel\ReportWishlist;
/**
 * Subscription Collection
 */
class Collection extends
    \Magento\Framework\Model\ResourceModel\Db\Collection\
    AbstractCollection {
    /**
     * Initialize resource collection
     *
     * @return void
     */
    public function _construct() {
        $this->_init('Magenest\MultipleWishlist\Model\ReportWishlist',
            'Magenest\MultipleWishlist\Model\ResourceModel\ReportWishlist');
    }
}