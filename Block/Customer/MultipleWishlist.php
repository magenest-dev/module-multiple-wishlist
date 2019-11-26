<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magenest\MultipleWishlist\Block\Customer;

use Magento\Catalog\Block\Product\AbstractProduct;
use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Class MultipleWishlist
 * @package Magenest\MultipleWishlist\Block\Customer
 */
class MultipleWishlist extends AbstractProduct
{
    /**
     * @var \Magenest\MultipleWishlist\Model\MultipleWishlistFactory
     */
    protected $multipleWishlistFactory;
    /**
     * @var \Magenest\MultipleWishlist\Model\ItemFactory
     */
    protected $mItemFactory;
    /**
     * @var \Magento\Wishlist\Controller\WishlistProviderInterface
     */
    protected $wishlistProvider;
    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productModelFactory;
    /**
     * @var \Magento\Store\Model\StoreManagerInterfaceFactory
     */
    protected $storeManagerInterface;
    /**
     * @var \Magento\Wishlist\Model\ResourceModel\Item\Option\CollectionFactory
     */
    protected $optionCollectionFactory;
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;
    /**
     * @var \Magento\Wishlist\Model\ResourceModel\Wishlist\CollectionFactory
     */
    protected $wishListCollectionFactory;

    /**
     * @var \Magento\Catalog\Helper\ImageFactory
     */
    protected $imageHelperFactory;

    /**
     * @var ScopeConfigInterface
     */
    public $_scopeConfig;

    /**
     * @var \Magenest\MultipleWishlist\Helper\Data
     */
    protected $helper;

	/**
	 * @var \Magento\Framework\Url\Helper\Data
	 */
	protected $urlHelper;

	/**
	 * @var \Magento\Review\Model\RatingFactory
	 */
	protected $ratingFactory;

	/**
	 * @var \Magento\Review\Model\ReviewFactory
	 */
	protected $reviewFactory;

	/**
	 * @var \Magento\Review\Block\Product\ReviewRenderer
	 */
	protected $reviewRenderer;

	/**
	 * @var ProductRepository
	 */
	protected $productRepository;

	/**
	 * MultipleWishlist constructor.
	 *
	 * @param Context $context
	 * @param \Magenest\MultipleWishlist\Model\MultipleWishlistFactory $multipleWishlistFactory
	 * @param \Magenest\MultipleWishlist\Model\ItemFactory $itemFactory
	 * @param \Magento\Wishlist\Controller\WishlistProviderInterface $wishlistProvider
	 * @param \Magento\Catalog\Model\ProductFactory $productModelFactory
	 * @param \Magento\Store\Model\StoreManagerInterface $storeManagerInterface
	 * @param \Magento\Wishlist\Model\ResourceModel\Item\Option\CollectionFactory $optionCollectionFactory
	 * @param \Magento\Customer\Model\Session $customerSession
	 * @param \Magento\Wishlist\Model\ResourceModel\Wishlist\CollectionFactory $wishListCollectionFactory
	 * @param \Magento\Catalog\Helper\ImageFactory $imageHelperFactory
	 * @param ScopeConfigInterface $scopeConfig
	 * @param \Magenest\MultipleWishlist\Helper\Data $helper
	 * @param \Magento\Framework\Url\Helper\Data $urlHelper
	 * @param \Magento\Review\Model\RatingFactory $ratingFactory
	 * @param \Magento\Review\Model\ReviewFactory $reviewFactory
	 * @param \Magento\Review\Block\Product\ReviewRenderer $reviewRenderer
	 * @param ProductRepository $productRepository
	 * @param array $data
	 */
    public function __construct(
        Context $context,
        \Magenest\MultipleWishlist\Model\MultipleWishlistFactory $multipleWishlistFactory,
        \Magenest\MultipleWishlist\Model\ItemFactory $itemFactory,
        \Magento\Wishlist\Controller\WishlistProviderInterface $wishlistProvider,
        \Magento\Catalog\Model\ProductFactory $productModelFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManagerInterface,
        \Magento\Wishlist\Model\ResourceModel\Item\Option\CollectionFactory $optionCollectionFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Wishlist\Model\ResourceModel\Wishlist\CollectionFactory $wishListCollectionFactory,
        \Magento\Catalog\Helper\ImageFactory $imageHelperFactory,
        ScopeConfigInterface $scopeConfig,
        \Magenest\MultipleWishlist\Helper\Data $helper,
        \Magento\Framework\Url\Helper\Data $urlHelper,
        \Magento\Review\Model\RatingFactory $ratingFactory,
        \Magento\Review\Model\ReviewFactory $reviewFactory,
        \Magento\Review\Block\Product\ReviewRenderer $reviewRenderer,
        ProductRepository $productRepository,
        array $data = []
    )
    {
        $this->mItemFactory = $itemFactory;
        $this->multipleWishlistFactory = $multipleWishlistFactory;
        $this->wishlistProvider = $wishlistProvider;
        $this->productModelFactory = $productModelFactory;
        $this->storeManagerInterface = $storeManagerInterface;
        $this->optionCollectionFactory = $optionCollectionFactory;
        $this->customerSession = $customerSession;
        $this->wishListCollectionFactory = $wishListCollectionFactory;
        $this->imageHelperFactory = $imageHelperFactory;
        $this->_scopeConfig = $scopeConfig;
        $this->helper = $helper;
        $this->urlHelper = $urlHelper;
        $this->ratingFactory = $ratingFactory;
        $this->reviewFactory = $reviewFactory;
        $this->reviewRenderer = $reviewRenderer;
        $this->productRepository = $productRepository;
        parent::__construct($context, $data);
    }


    /**
     * @return array
     */
    public function getWishlist()
    {
        $wishlist = $this->multipleWishlistFactory->create();
        $list = $wishlist->loadWishlist();
        return $list;
    }

    /**
     * @return int
     */
    public function getCountWishlist()
    {
        $wishlist = $this->multipleWishlistFactory->create();
        $number = $wishlist->getCountWishlist();
        return $number;
    }

    /**
     * @return int|mixed
     */
    public function getTabCookieId()
    {
        $wishlistAddId = $this->getRequest()->getParam('addNewId');
        $wishlistId = $this->getRequest()->getParam('wishlistId');
        if($wishlistId) return $wishlistId;
        elseif($wishlistAddId) return $wishlistAddId;
        return 0;
    }
    /**
     * @param $wishlistId
     * @return array of items
     */
    public function getItems($wishlistId)
    {
        $itemFactory = $this->mItemFactory->create();
        $return = $itemFactory->getItems($wishlistId);

        return $return;
    }
    /**
     * @return array
     */

    public function getMainWishlist()
    {
        $currentUserWishlist = $this->wishlistProvider->getWishlist();

        $return = array();

        if ($currentUserWishlist) {
            $wishlistItems = $currentUserWishlist->getItemCollection();
            $items = $wishlistItems->getData();

            foreach ($items as $item) {
                $store = $this->storeManagerInterface->getStore();
                $pr = array();
                $product = $this->productModelFactory->create()->load($item['product_id']);
                if($product->getId()){
                $image = $product->getImage();
                $pr['id'] = $item['wishlist_item_id'];
                $pr['product_url'] = $product->getProductUrl();
                $pr['price'] = $this->helper->renderPrice($product);;
                if($image=="no_selection" || $image==null)
                {
                    $pr['img_link']=$this->imageHelperFactory->create()->getDefaultPlaceholderUrl('image');
                }else{
                    $pr['img_link'] = $store->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'catalog/product' . $product->getImage();
                }
                $pr['product_name'] = $product->getName();
                $pr['description'] = $item['description'];
                $pr['qty'] = $item['qty'];
                $pr['product'] = $product;
                array_push($return, $pr);
                }
            }
        }
        return $return;
    }

    /**
     * @return mixed|string
     */
    public function getTabId()
    {
        if ($this->getRequest()->getParam('wishlist')) {
            return $this->getRequest()->getParam('wishlist');
        }
        return '0';
    }

    /**
     * @return int|null
     */
    public function getCustomerId()
    {
        if ($this->customerSession->isLoggedIn()) {
            $customerId = $this->customerSession->getCustomerId();
            return $customerId;
        }
    }

    /**
     * @return mixed
     */
    public function getWishListMainId()
    {
        $customerId = $this->getCustomerId();
        $wishListId = $this->wishListCollectionFactory->create()->addfieldtofilter('customer_id', $customerId)->getData();
        return $wishListId[0]['wishlist_id'];
    }

    /**
     * get product in wishlist main customer if have.
     * @param $customerId
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getWishListMain($collectionWishList)
    {
        $return = array();
        foreach ($collectionWishList as $item) {
            $product = $this->productModelFactory->create()->load($item['entity_id']);
            array_push($return, $this->helper->getProductSendMail($product));
        }
        return $return;
    }

	/**
	 * @param $collectionWishList
	 * @param null $customerId
	 * @return array
	 * @throws \Magento\Framework\Exception\NoSuchEntityException
	 */
	public function getProductOutOfStock($collectionWishList)
    {
        $return = array();
        foreach ($collectionWishList as $item) {
            $product = $this->productRepository->getById($item['entity_id']);
            array_push($return, $this->helper->getProductSendMail($product));
        }
        return $return;
    }

/*addNew*/
    /**
     * Retrieve list toolbar HTML
     *
     * @return string
     */
    public function getToolbarHtml()
    {
        return $this->getChildHtml('toolbar');
    }

	/**
	 * @param Product $product
	 * @return array
	 */
	public function getAddToCartPostParams(Product $product)
    {
        $url = $this->getAddToCartUrl($product);
        return [
            'action' => $url,
            'data' => [
                'product' => $product->getEntityId(),
                ActionInterface::PARAM_NAME_URL_ENCODED => $this->urlHelper->getEncodedUrl($url),
            ]
        ];
    }
}
