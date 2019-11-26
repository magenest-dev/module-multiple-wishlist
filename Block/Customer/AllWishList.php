<?php

namespace Magenest\MultipleWishlist\Block\Customer;


use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Pricing\Helper\Data as PricingHelper;
use Magento\Framework\Registry;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Sharing
 * @package Magenest\MultipleWishlist\Block\Share
 */
class AllWishList extends \Magenest\MultipleWishlist\Block\AbstractBlock
{

    /**
     * @var string
     */
    protected $multipleWishlistModelFactory;
    /**
     * @var \Magenest\MultipleWishlist\Model\ResourceModel\MultipleWishlist\CollectionFactory
     */
    protected $multipleWishlistCollectionFactory;
    /**
     * @var \Magenest\MultipleWishlist\Model\ItemFactory
     */
    protected $itemFactory;
    /**
     * @var Registry
     */
    public $_coreRegistry;
    /**
     * @var ScopeConfigInterface
     */
    public $_scopeConfig;
    /**
     * @var
     */
    public $_product;
    /**
     * @var PricingHelper
     */
    public $_priceHelper;
    /**
     * @var \Magento\Wishlist\Controller\WishlistProviderInterface
     */
    protected $wishlistProvider;
    /**
     * @var \Magento\Store\Model\StoreManagerInterfaceFactory
     */
    protected $storeManagerInterface;
    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productModelFactory;
    /**
     * @var \Magento\Wishlist\Helper\Data
     */
    protected $_wishlistHelper;
    /**
     * @var \Magento\Wishlist\Model\ResourceModel\Wishlist\CollectionFactory
     */
    protected $wishListCollectionFactory;
    /**
     * @var \Magento\Wishlist\Model\ResourceModel\Item\CollectionFactory
     */
    protected $wishListItemCollectionFactory;


    /**
     * Sharing constructor.
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param \Magenest\MultipleWishlist\Helper\Data $multipleWishlistHelper
     * @param \Magenest\MultipleWishlist\Model\MultipleWishlistFactory $multipleWishlistModelFactory
     * @param \Magenest\MultipleWishlist\Model\ResourceModel\MultipleWishlist\CollectionFactory $multipleWishlistCollectionFactory
     * @param \Magenest\MultipleWishlist\Model\ItemFactory $itemFactory
     * @param Registry $coreRegistry
     * @param PricingHelper $PricingHelper
     * @param ScopeConfigInterface $scopeConfig
     * @param \Magento\Wishlist\Controller\WishlistProviderInterface $wishlistProvider
     * @param \Magento\Store\Model\StoreManagerInterfaceFactory $storeManagerInterface
     * @param \Magento\Catalog\Model\ProductFactory $productModelFactory
     * @param \Magento\Wishlist\Helper\Data $wishlistHelper
     * @param \Magento\Wishlist\Model\ResourceModel\Wishlist\CollectionFactory $wishListCollectionFactory
     * @param \Magento\Wishlist\Model\ResourceModel\Item\CollectionFactory $wishListItemCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magenest\MultipleWishlist\Helper\Data $multipleWishlistHelper,
        \Magenest\MultipleWishlist\Model\MultipleWishlistFactory $multipleWishlistModelFactory,
        \Magenest\MultipleWishlist\Model\ResourceModel\MultipleWishlist\CollectionFactory $multipleWishlistCollectionFactory,
        \Magenest\MultipleWishlist\Model\ItemFactory $itemFactory,
        Registry $coreRegistry,
        PricingHelper $PricingHelper,
        ScopeConfigInterface $scopeConfig,
        \Magento\Wishlist\Controller\WishlistProviderInterface $wishlistProvider,
        \Magento\Store\Model\StoreManagerInterfaceFactory $storeManagerInterface,
        \Magento\Catalog\Model\ProductFactory $productModelFactory,
        \Magento\Wishlist\Helper\Data $wishlistHelper,
        \Magento\Wishlist\Model\ResourceModel\Wishlist\CollectionFactory $wishListCollectionFactory,
        \Magento\Wishlist\Model\ResourceModel\Item\CollectionFactory $wishListItemCollectionFactory,
        array $data)
    {
        $this->multipleWishlistModelFactory = $multipleWishlistModelFactory;
        $this->multipleWishlistCollectionFactory = $multipleWishlistCollectionFactory;
        $this->itemFactory = $itemFactory;
        $this->_scopeConfig = $scopeConfig;
        $this->_priceHelper = $PricingHelper;
        $this->_coreRegistry = $coreRegistry;
        $this->wishlistProvider = $wishlistProvider;
        $this->storeManagerInterface = $storeManagerInterface;
        $this->productModelFactory = $productModelFactory;
        $this->_wishlistHelper = $wishlistHelper;
        $this->wishListCollectionFactory = $wishListCollectionFactory;
        $this->wishListItemCollectionFactory = $wishListItemCollectionFactory;
        parent::__construct($context, $httpContext, $multipleWishlistHelper, $data);
    }

     /**
     * @param \Magento\Catalog\Model\Product $product
     * @param array $additional
     * @return string
     */
    public function getProductUrl($product, $additional = [])
    {
        $additional['_scope_to_url'] = true;
        return parent::getProductUrl($product, $additional);
    }

    /**
     * @param $wishlistId
     * @return int|void
     */
    public function getWishlistItemCount($wishlistId)
    {
        /**         * @var $wl \Magenest\MultipleWishlist\Model\MultipleWishlist */
        $wl = $this->multipleWishlistModelFactory->create()->load($wishlistId);
        return count($wl->getProducts());
    }

    /**
     * Retrieve Wishlist Product Items collection
     *
     * @return \Magenest\MultipleWishlist\Model\ResourceModel\Item\Collection
     */
    public function getWishlistItems($wishlistId)
    {
        /**
         * @var $wl \Magenest\MultipleWishlist\Model\MultipleWishlist
         */
        $wl = $this->multipleWishlistModelFactory->create()->load($wishlistId);
        return $wl->getProductsSharing();
    }

    /**
     * @return \Magenest\MultipleWishlist\Helper\Data|\Magento\Wishlist\Helper\Data
     */
    protected function _getHelper()
    {
        return $this->_wishlistHelper;
    }

    /**
     * @return \Magenest\MultipleWishlist\Model\MultipleWishlist|\Magento\Wishlist\Model\Wishlist
     */
    protected function _getWishlist()
    {
        return $this->_getHelper()->getWishlist();
    }

    /**
     * @return int
     */
    public function getWishlistItemsCountMain()
    {
        return $this->_getWishlist()->getItemsCount();
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getMainWishlist()
    {
        $data = $this->getRequest()->getParams();
        $wishListId = $data['wishListMainId'];
        $productId = $this->wishListItemCollectionFactory->create()->addFieldToFilter('wishlist_id', $wishListId)->getData();
        $return = array();
        foreach ($productId as $item) {
            $store = $this->storeManagerInterface->create()->getStore();
            $pr = array();
            $product = $this->productModelFactory->create()->load($item['product_id']);
            $pr['id'] = $item['wishlist_item_id'];
            $pr['product_url'] = $product->getProductUrl();
            $pr['price'] = $product->getFormatedPrice();
            $pr['img_link'] = $store->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'catalog/product' . $product->getImage();
            $pr['product_name'] = $product->getName();
            $pr['description'] = $item['description'];
            $pr['qty'] = $item['qty'];
            array_push($return, $pr);
        }
        return $return;
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCurrentUrl()
    {
        return $this->_storeManager->getStore()->getCurrentUrl();
    }

}
