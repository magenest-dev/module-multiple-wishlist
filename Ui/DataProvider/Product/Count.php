<?php
namespace Magenest\MultipleWishlist\Ui\DataProvider\Product;
class Count implements \Magento\Ui\DataProvider\AddFieldToCollectionInterface
{
    public function addField(\Magento\Framework\Data\Collection $collection, $field, $alias = null){
        $collection->joinField(
            'count',
            'magenest_reportwishlist',
            'count',
            'product_id=entity_id',
            null,
            'left'
        );
    }
}