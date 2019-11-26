<?php
namespace Magenest\MultipleWishlist\Model\Config\Source;
class Days implements
    \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            [
                'value' => 'D',
                'label' => __('Day')
            ],
            [
                'value' => 'W',
                'label' => __('Weekly')
            ],
            [
                'value' => 'M',
                'label' => __('Monthly')
            ]
        ];
    }
}