<?php

namespace Magenest\MultipleWishlist\Block\Share\Email;

class Items extends \Magenest\MultipleWishlist\Block\AbstractBlock
{
    /**
     * @var string
     */
    protected $_template = 'email/items.phtml';
    protected $multipleWishlistModelFactory;
    protected $itemFactory;
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magenest\MultipleWishlist\Helper\Data $multipleWishlistHelper,
        \Magenest\MultipleWishlist\Model\MultipleWishlistFactory $multipleWishlistModelFactory,
        \Magenest\MultipleWishlist\Model\ItemFactory $itemFactory,
        array $data)
    {
        $this->multipleWishlistModelFactory = $multipleWishlistModelFactory;
        $this->itemFactory = $itemFactory;
        parent::__construct($context, $httpContext, $multipleWishlistHelper, $data);
    }


    /**
     * Retrieve Product View URL
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param array $additional
     * @return string
     */
    public function getProductUrl($product, $additional = [])
    {
        $additional['_scope_to_url'] = true;
        return parent::getProductUrl($product, $additional);
    }

    public function getWishlistItemCount($wishlistId)
    {
        /**         * @var $wl \Magenest\MultipleWishlist\Model\MultipleWishlist */
        $wl = $this->multipleWishlistModelFactory->create()->load($wishlistId);
        return count($wl->getProducts());
    }

    /**
     * Retrieve URL for add product to shopping cart
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param array $additional
     * @return string
     */
    public function getAddToCartUrl($product, $additional = [])
    {
        $additional['nocookie'] = 1;
        $additional['_scope_to_url'] = true;
        return parent::getAddToCartUrl($product, $additional);
    }

    public function getWishlistId()
    {
        $data = $this->getRequest()->getParams();
        $wishListId = $data['wishlistId'];
        return $wishListId;
    }

    /**
     * Retrieve Wishlist Product Items collection
     *
     * @return \Magenest\MultipleWishlist\Model\ResourceModel\Item\Collection
     */
    public function getWishlistItems($wishlistId)
    {
        /**         * @var $wl \Magenest\MultipleWishlist\Model\MultipleWishlist */
   /*     $wl = $this->multipleWishlistModelFactory->create()->load($wishlistId);*/
        return $this->getProducts($wishlistId);
    }
    public function getProducts($wishlistId)
    {
        $return = array();
            $item = $this->itemFactory->create();
            $return = $item->getItems($wishlistId);
            return $return;
    }
}
