<?php
namespace Magenest\MultipleWishlist\Model\Item;

use Magento\Catalog\Api\ProductRepositoryInterface;

class Option extends \Magento\Framework\Model\AbstractModel implements
    \Magento\Catalog\Model\Product\Configuration\Item\Option\OptionInterface
{
    protected $itemFactory;
    /**
     * Initialize resource model
     *
     * @return void
     */

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    protected $optionFactory;

    protected $_resourceOption ;
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magenest\MultipleWishlist\Model\Item\OptionFactory $optionFactory,
        \Magenest\MultipleWishlist\Model\ResourceModel\Item\Option $resource,
        \Magenest\MultipleWishlist\Model\ResourceModel\Item\Option\Collection $resourceCollection,
        \Magenest\MultipleWishlist\Model\ItemFactory $itemFactory,
        ProductRepositoryInterface $productRepository,
        array $data = []
    )
    {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->itemFactory = $itemFactory;
        $this->productRepository = $productRepository;
        $this->optionFactory = $optionFactory;
        $this->_resourceOption = $resource;
    }

    /**
     * Retrieve value associated with this option
     *
     * @return mixed
     */
    public function getValue()
    {
        $value = $this->_getData('value');
        return $value;
    }

    public function addOption($productId, $itemId, $cartCandidates, $storeId)
    {

        //save the option of the item in the table
        foreach ($cartCandidates as $candidate) {
            if ($candidate->getParentProductId()) {
                continue;
            }
            $candidate->setWishlistStoreId($storeId);

            $qty = $candidate->getQty() ? $candidate->getQty() : 1;

            $options = $candidate->getCustomOptions();

            foreach ($options as $option) {
                /** @var $tmpOption \Magenest\MultipleWishlist\Model\Item\Option */
                $tmpOption = $this->optionFactory->create();
                $tmpOption->setData('multiplewishlist_item_id', $itemId);
                $tmpOption->setData('product_id', $productId);
                $tmpOption->setData('code', $option->getData('code'));
                $tmpOption->setData('value', $option->getValue());
              /*  $this->_resourceOption->save($tmpOption);*/
                $tmpOption->save();
                unset($tmpOption);
            }
//            $this->setData('code', $optionCode);
//            $this->setData('value', $option->getValue());
        }
    }
}