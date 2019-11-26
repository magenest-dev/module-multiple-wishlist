<?php

namespace Magenest\MultipleWishlist\Block\Share;

use Magento\Backend\Block\Template;

/**
 * Class MultipleWishlist
 * @package Magenest\MultipleWishlist\Block\Share
 */
class MultipleWishlist extends \Magenest\MultipleWishlist\Block\AbstractBlock
{

    /**
     * @var string
     */
    protected $_template = 'shared.phtml';
    /**
     * @var \Magenest\MultipleWishlist\Model\MultipleWishlistFactory
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
     * @var \Magento\Wishlist\Model\ResourceModel\Wishlist\CollectionFactory
     */
    protected $wishListCollectionFactory;
    /**
     * @var \Magento\Wishlist\Model\ResourceModel\Item\CollectionFactory
     */
    protected $wishListItemCollectionFactory;
    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productModelFactory;
    /**
     * @var
     */
    protected $storeManager;

    protected $imageHelperFactory;
    /**
     * MultipleWishlist constructor.
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param \Magenest\MultipleWishlist\Helper\Data $multipleWishlistHelper
     * @param \Magenest\MultipleWishlist\Model\MultipleWishlistFactory $multipleWishlistModelFactory
     * @param \Magenest\MultipleWishlist\Model\ResourceModel\MultipleWishlist\CollectionFactory $multipleWishlistCollectionFactory
     * @param \Magenest\MultipleWishlist\Model\ItemFactory $itemFactory
     * @param \Magento\Wishlist\Model\ResourceModel\Wishlist\CollectionFactory $wishListCollectionFactory
     * @param \Magento\Wishlist\Model\ResourceModel\Item\CollectionFactory $wishListItemCollectionFactory
     * @param \Magento\Catalog\Model\ProductFactory $productModelFactory
     * @param \Magento\Store\Model\StoreManagerInterfaceFactory $storeManagerInterface
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magenest\MultipleWishlist\Helper\Data $multipleWishlistHelper,
        \Magenest\MultipleWishlist\Model\MultipleWishlistFactory $multipleWishlistModelFactory,
        \Magenest\MultipleWishlist\Model\ResourceModel\MultipleWishlist\CollectionFactory $multipleWishlistCollectionFactory,
        \Magenest\MultipleWishlist\Model\ItemFactory $itemFactory,
        \Magento\Wishlist\Model\ResourceModel\Wishlist\CollectionFactory $wishListCollectionFactory,
        \Magento\Wishlist\Model\ResourceModel\Item\CollectionFactory $wishListItemCollectionFactory,
        \Magento\Catalog\Model\ProductFactory $productModelFactory,
        \Magento\Store\Model\StoreManagerInterfaceFactory $storeManagerInterface,
        \Magento\Catalog\Helper\ImageFactory $imageHelperFactory,
        array $data)
    {
        $this->multipleWishlistModelFactory = $multipleWishlistModelFactory;
        $this->multipleWishlistCollectionFactory = $multipleWishlistCollectionFactory;
        $this->itemFactory = $itemFactory;
        $this->wishListCollectionFactory = $wishListCollectionFactory;
        $this->wishListItemCollectionFactory = $wishListItemCollectionFactory;
        $this->productModelFactory = $productModelFactory;
        $this->storeManager = $storeManagerInterface;
        $this->imageHelperFactory = $imageHelperFactory;
        parent::__construct($context, $httpContext, $multipleWishlistHelper, $data);
    }

    /**
     * Prepare global layout
     *
     * @return $this
     *
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->pageConfig->getTitle()->set($this->getHeader());
        return $this;
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
        /*       $wl = $this->multipleWishlistModelFactory->create()->load($wishlistId);*/
        return count($this->getProducts($wishlistId));
    }

    /**
     * @param $wishlistId
     * @return int|void
     */
    public function getWishlistItemMainCount($wishlistId)
    {
        /**         * @var $wl \Magenest\MultipleWishlist\Model\MultipleWishlist */
        /*       $wl = $this->multipleWishlistModelFactory->create()->load($wishlistId);*/
        return count($this->getWishlistMainItems($wishlistId));
    }

    /**
     * @param $wishlistId
     * @return array
     */
    public function getProducts($wishlistId)
    {
        $return = array();
        $item = $this->itemFactory->create();
        $return = $item->getItems($wishlistId);
        return $return;
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
        /*  $wl = $this->multipleWishlistModelFactory->create()->load($wishlistId);*/
        return $this->getProducts($wishlistId);
    }

    /**
     * @param $wishlistId
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getWishlistMainItems($wishlistId)
    {
        /**
         * @var $wl \Magenest\MultipleWishlist\Model\MultipleWishlist
         */
        /*  $wl = $this->multipleWishlistModelFactory->create()->load($wishlistId);*/
        $productId = $this->wishListItemCollectionFactory->create()->addFieldToFilter('wishlist_id', $wishlistId)->getData();
        $return = array();
        foreach ($productId as $item) {
            $store = $this->storeManager->create()->getStore();
            $pr = array();
            $product = $this->productModelFactory->create()->load($item['product_id']);
            $image = $product->getImage();
            $pr['id'] = $item['wishlist_item_id'];
            $pr['product_url'] = $product->getProductUrl();
            $pr['price'] = $product->getFormatedPrice();
            if($image=="no_selection" || $image==null)
            {
                $pr['img_link']=$this->imageHelperFactory->create()->getDefaultPlaceholderUrl('image');
            }else{
                $pr['img_link'] = $store->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'catalog/product' . $product->getImage();
            }            $pr['product_name'] = $product->getName();
            $pr['description'] = $item['description'];
            $pr['qty'] = $item['qty'];
            array_push($return, $pr);
        }
        return $return;
    }

    /**
     * @return array
     */
    public function getWishlistId()
    {
        $array[] = null;
        $data = $this->getRequest()->getParams();
        $sharingCode = $data['code'];
        $collection = $this->multipleWishlistCollectionFactory->create()->addfieldtofilter('sharing_code', $sharingCode)->getData();
        if (!empty($collection)) {
            $wishListId = $collection[0]['id'];
        } else $wishListId = null;
        $collectionMain = $this->wishListCollectionFactory->create()->addfieldtofilter('sharing_code', $sharingCode)->getData();
        if (!empty($collectionMain)) {
            $wishListMainId = $collectionMain[0]['wishlist_id'];
        } else $wishListMainId = null;
        $array['wishListId'] = $wishListId;
        $array['wishListMainId'] = $wishListMainId;
        return $array;
    }

    /**
     * @return string
     */
    public function getSharedAddAllToCartUrl()
    {
        return $this->_getHelper()->getSharedAddAllToCartUrl();
    }

    /**
     * @return \Magenest\MultipleWishlist\Helper\Data|\Magento\Wishlist\Helper\Data
     */
    protected function _getHelper()
    {
        return $this->_wishlistHelper;
    }

    /**
     * Retrieve Page Header
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeader()
    {
        return __("Shared Wish List");
    }
}
