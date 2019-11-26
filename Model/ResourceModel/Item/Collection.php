<?php
namespace Magenest\MultipleWishlist\Model\ResourceModel\Item;

//use Magento\Catalog\Api\Data\ProductInterface;
//use Magento\Framework\EntityManager\MetadataPool;


class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Product Visibility Filter to product collection flag
     *
     * @var bool
     */
    protected $_productVisible = false;
    /**
     * Initialize resource model for collection
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('Magenest\MultipleWishlist\Model\Item', 'Magenest\MultipleWishlist\Model\ResourceModel\Item');
        $this->addFilterToMap('store_id', 'main_table.store_id');
    }

    /**
     * Add filter by wishlist object
     *
     * @param \Magento\Wishlist\Model\Wishlist $wishlist
     * @return $this
     */
    public function addWishlistFilter(\Magenest\MultipleWishlist\Model\MultipleWishlist $wishlist)
    {
        $this->addFieldToFilter('wishlist_id', $wishlist->getId());
        return $this;
    }

    /**
     * Add filter by shared stores
     *
     * @param array $storeIds
     * @return $this
     */
    public function addStoreFilter($storeIds = [])
    {
        if (!is_array($storeIds)) {
            $storeIds = [$storeIds];
        }
        $this->_storeIds = $storeIds;
        $this->addFieldToFilter('store_id', ['in' => $this->_storeIds]);

        return $this;
    }

    /**
     * Set product Visibility Filter to product collection flag
     *
     * @param bool $flag
     * @return $this
     */
    public function setVisibilityFilter($flag = true)
    {
        $this->_productVisible = (bool)$flag;
        return $this;
    }


}
