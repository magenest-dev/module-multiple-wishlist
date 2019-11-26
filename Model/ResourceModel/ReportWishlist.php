<?php
namespace Magenest\MultipleWishlist\Model\ResourceModel;
class ReportWishlist extends
    \Magento\Framework\Model\ResourceModel\Db\AbstractDb {
    public function _construct() {
        $this->_init('magenest_reportwishlist',
            'entity_id');
    }
}