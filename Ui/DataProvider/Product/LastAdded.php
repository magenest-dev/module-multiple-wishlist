<?php
namespace Magenest\MultipleWishlist\Ui\DataProvider\Product;
class LastAdded implements \Magento\Ui\DataProvider\AddFieldToCollectionInterface
{
    public function addField(\Magento\Framework\Data\Collection $collection, $field, $alias = null){

        $collection->joinField(
            'last_added',
            'magenest_reportwishlist',
            'last_added',
            'product_id=entity_id',
            null,
            'left'
        );
    }
}