<?php
namespace Magenest\MultipleWishlist\Helper;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\View\Element\Template;
use phpDocumentor\Reflection\Types\Null_;

/**
 * Class Data
 *
 * @package Magenest\MultipleWishlist\Helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Config key 'Display Wishlist Summary'
     */
    const XML_PATH_WISHLIST_LINK_USE_QTY = 'wishlist/wishlist_link/use_qty';

    /**
     * Config key 'Display Out of Stock Products'
     */
    const XML_PATH_CATALOGINVENTORY_SHOW_OUT_OF_STOCK = 'cataloginventory/options/show_out_of_stock';

    /**
     * Currently logged in customer
     *
     * @var \Magento\Customer\Api\Data\CustomerInterface
     */
    protected $_currentCustomer;

    /**
     * Customer Wishlist instance
     *
     * @var \Magenest\MultipleWishlist\Model\MultipleWishlist
     */
    protected $_multipleWishlist;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;


    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var \Magenest\MultipleWishlist\Model\MultipleWishlistFactory
     */
    protected $_multipleWishlistFactory;

    /**
     * @var \Magenest\MultipleWishlist\Controller\MultipleWishlistProviderInterface
     */
    protected $_multipleWishlistProvider;
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magenest\MultipleWishlist\Model\ItemFactory
     */
    protected $itemFactory;

    /**
     * @var \Magenest\MultipleWishlist\Model\Item\OptionFactory
     */
    protected $optionFactory;

    /**
     * @var \Magenest\MultipleWishlist\Model\ResourceModel\Item\CollectionFactory
     */
    protected $optionCollectionFactory;

    /**
     * @var \Magento\Catalog\Helper\Product\Configuration
     */
    protected $configurationHelper;

    /**
     * @var \Magento\Framework\Data\Helper\PostHelper
     */
    protected $_postDataHelper;

    /**
     * @var \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory
     */
    protected $customerCollection;

    /**
     * @var \Magento\Wishlist\Model\WishlistFactory
     */
    protected $wishlistFactory;

    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $transportBuilder;

	/**
	 * @var
	 */
	protected $searchCriteria;

	/**
	 * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
	 */
	protected $productCollectionFactory;

	/**
	 * @var \Magento\Catalog\Model\Product\Attribute\Source\Status
	 */
	protected $productStatus;

	/**
	 * @var \Magento\Catalog\Model\Product\Visibility
	 */
	protected $productVisibility;

	/**
	 * @var \Magento\Wishlist\Model\ResourceModel\Wishlist\CollectionFactory
	 */
	protected $collectionFactory;

	/**
	 * @var \Magento\Framework\Serialize\Serializer\Json
	 */
	protected $serializer;

	/**
	 * @var \Magento\CatalogInventory\Model\Stock\StockItemRepository
	 */
	protected $_stockItemRepository;

	/**
	 * @var \Magento\Framework\App\Config\ScopeConfigInterface
	 */
	protected $_scopeConfig;

	/**
	 * @var \Magento\Catalog\Model\ProductFactory
	 */
	protected $productModelFactory;

	/**
	 * @var \Magento\Wishlist\Model\ResourceModel\Item\CollectionFactory
	 */
	protected $itemCollectionFactory;

	/**
	 * @var \Magenest\MultipleWishlist\Model\ResourceModel\MultipleWishlist\CollectionFactory
	 */
	protected $multipleWishlistCollection;

	/**
	 * @var \Magenest\MultipleWishlist\Model\ResourceModel\Item\CollectionFactory
	 */
	protected $itemMultipleWishlistCollection;

	/**
	 * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
	 */
	protected $timezone;

	/**
	 * @var \Magento\Wishlist\Model\ItemFactory
	 */
	protected $itemWishListFactory;

	/**
	 * @var \Magento\Catalog\Helper\ImageFactory
	 */
	protected $imageHelperFactory;

	/**
	 * @var \Magento\InventorySalesAdminUi\Model\GetSalableQuantityDataBySku
	 */
	protected $getSalableQuantityDataBySku;

	/**
	 * @var \Magento\Wishlist\Model\ResourceModel\Wishlist
	 */
	protected $resourceWishList;

	/**
	 * @var \Magento\Review\Model\ReviewFactory
	 */
	protected $reviewFactory;

	/**
	 * @var \Magento\Framework\Pricing\Helper\Data
	 */
	protected $priceHelper;

	protected $moduleManager;
    /**
     * Data constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magenest\MultipleWishlist\Model\MultipleWishlistFactory $mulipleWishlistFactory
     * @param \Magenest\MultipleWishlist\Model\ItemFactory $itemFactory
     * @param \Magenest\MultipleWishlist\Model\Item\OptionFactory $optionFactory
     * @param \Magenest\MultipleWishlist\Model\ResourceModel\Item\CollectionFactory $optionCollectionFactory
     * @param \Magento\Catalog\Helper\Product\Configuration $configuration
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Data\Helper\PostHelper $postDataHelper
     * @param \Magenest\MultipleWishlist\Controller\MultipleWishlistProviderInterface $multipleWishlistProviderInterface
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerCollection
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Customer\Model\Session $customerSession,
        \Magenest\MultipleWishlist\Model\MultipleWishlistFactory $mulipleWishlistFactory,
        \Magenest\MultipleWishlist\Model\ItemFactory $itemFactory,
        \Magenest\MultipleWishlist\Model\Item\OptionFactory $optionFactory,
        \Magenest\MultipleWishlist\Model\ResourceModel\Item\CollectionFactory $optionCollectionFactory,
        \Magento\Catalog\Helper\Product\Configuration $configuration,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Data\Helper\PostHelper $postDataHelper,
        \Magenest\MultipleWishlist\Controller\MultipleWishlistProviderInterface $multipleWishlistProviderInterface,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerCollection,
        \Magento\Wishlist\Model\WishlistFactory $wishlistFactory,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\Product\Attribute\Source\Status $productStatus,
        \Magento\Catalog\Model\Product\Visibility $productVisibility,
        \Magento\Wishlist\Model\ResourceModel\Wishlist\CollectionFactory $collectionFactory,
        \Magento\Framework\Serialize\Serializer\Json $serializer,
        \Magento\CatalogInventory\Model\Stock\StockItemRepository $stockItemRepository,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Catalog\Model\ProductFactory $productModelFactory,
        \Magento\Wishlist\Model\ResourceModel\Item\CollectionFactory $itemCollectionFactory,
        \Magenest\MultipleWishlist\Model\ResourceModel\MultipleWishlist\CollectionFactory $multipleWishlistCollection,
        \Magenest\MultipleWishlist\Model\ResourceModel\Item\CollectionFactory $itemMultipleWishlistCollection,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Magento\Wishlist\Model\ItemFactory $itemWishListFactory,
        \Magento\Catalog\Helper\ImageFactory $imageHelperFactory,
        \Magento\Wishlist\Model\ResourceModel\Wishlist $resourceWishList,
        \Magento\Review\Model\ReviewFactory $reviewFactory,
		\Magento\Framework\Pricing\Helper\Data $priceHelper,
		\Magento\Framework\Module\Manager $moduleManager
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_customerSession = $customerSession;
        $this->_storeManager = $storeManager;
        $this->_postDataHelper = $postDataHelper;
        $this->productRepository = $productRepository;
        $this->_multipleWishlistFactory = $mulipleWishlistFactory;
        $this->_multipleWishlistProvider = $multipleWishlistProviderInterface;
        $this->itemFactory = $itemFactory;
        $this->optionFactory = $optionFactory;
        $this->optionCollectionFactory = $optionCollectionFactory;
        $this->configurationHelper = $configuration;
        $this->customerCollection = $customerCollection;
        $this->wishlistFactory = $wishlistFactory;
        $this->transportBuilder = $transportBuilder;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->productStatus = $productStatus;
        $this->productVisibility = $productVisibility;
        $this->collectionFactory = $collectionFactory;
        $this->serializer = $serializer;
        $this->_stockItemRepository = $stockItemRepository;
        $this->_scopeConfig = $scopeConfig;
        $this->productModelFactory = $productModelFactory;
        $this->itemCollectionFactory = $itemCollectionFactory;
        $this->multipleWishlistCollection = $multipleWishlistCollection;
        $this->itemMultipleWishlistCollection = $itemMultipleWishlistCollection;
        $this->timezone = $timezone;
        $this->itemWishListFactory = $itemWishListFactory;
        $this->imageHelperFactory = $imageHelperFactory;
        $this->resourceWishList = $resourceWishList;
        $this->reviewFactory = $reviewFactory;
        $this->priceHelper      = $priceHelper;
		$this->moduleManager = $moduleManager;
        parent::__construct($context);
    }

    /**
     * @return bool
     */
    public function isAllow()
    {
        return true;
    }

    /**
     * @param $itemId
     * @return array
     */
    public function getOptionByCode($itemId, $code)
    {
        $optionArr=[];
        $optionCollection = $this->optionCollectionFactory->create()
            ->addFieldToFilter('multiplewishlist_item_id', $itemId)
            ->addFieldToFilter('code', $code);

        if ($optionCollection->getSize() > 0) {
            foreach ($optionCollection as $option) {
                $optionArr[$option->getCode()] = $option;

            }
        }

        // var_dump($optionArr);
        return $optionArr;
    }

    /**
     * @param $item
     * @return is
     */
    public function getOptions($item)
    {
        /**
 * @var  $options is array contains string key and value
*/
        $options = $this->configurationHelper->getOptions($item);
        return $options;
    }

    /**
     * @param $itemId
     * @return array
     */
    public function getCustomOptionAsArr($itemId)
    {
        $optionArr=[];
        $optionCollection = $this->optionCollectionFactory->create()
            ->addFieldToFilter('multiplewishlist_item_id', $itemId);

        if ($optionCollection->getSize() > 0) {
            foreach ($optionCollection as $option) {
                $optionArr[$option->getCode()] = $option;

            }
        }

        return $optionArr;
    }

    /**
     * Retrieve wishlist by logged in customer
     *
     * @return \Magenest\MultipleWishlist\Model\MultipleWishlist
     */
    public function getWishlist()
    {
        if ($this->_multipleWishlist === null) {
            if ($this->_coreRegistry->registry('shared_wishlist')) {
                $this->_multipleWishlist = $this->_coreRegistry->registry('shared_wishlist');
            } else {
                $this->_multipleWishlist = $this->_multipleWishlistProvider->getWishlist();
            }
        }
        return $this->_multipleWishlist;
    }


    /**
     * @param $item
     * @param array $additional
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getProductUrl($item, $additional = [])
    {
        if ($item instanceof \Magento\Catalog\Model\Product) {
            $product = $item;
        } else {
            $product = $item->getItems();
        }
        $buyRequest = $item->getBuyRequest();
        if (is_object($buyRequest)) {
            $config = $buyRequest->getSuperProductConfig();
            if ($config && !empty($config['product_id'])) {
                $product = $this->productRepository->getById(
                    $config['product_id'],
                    false,
                    $this->_storeManager->getStore()->getStoreId()
                );
            }
        }
        return $product->getUrlModel()->getUrl($product, $additional);
    }

    /**
     * Get data all customer
     * @return array
     */
    public function getAllCustomer()
    {
        $customerData = $this->customerCollection->create()->getData();
        return $customerData;
    }

    /**
     * Get product in wish list main by customer id
     * @param $customerId
     * @return \Magento\Wishlist\Model\ResourceModel\Item\Collection
     */
    public function getProductInMain($customerId)
    {
        $numberCheckConfig = $this->_scopeConfig->getValue('multiplewishlist/cronsendmail/schedule', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $todayDate = $this->timezone->date()->format('Y-m-d');
        $multipleWishlistCollection = $this->multipleWishlistCollection->create()->addFieldToFilter('customer_id', $customerId)->getItems();
        $array = array();
        $productId = array();
        if (count($multipleWishlistCollection)) {
            foreach ($multipleWishlistCollection as $key => $value) {
                $array[] = $key;
            }
            $itemMultipleWishlistCollection = $this->itemMultipleWishlistCollection->create()->addFieldToFilter('wishlist_id', array('in' => $array));
            foreach ($itemMultipleWishlistCollection as $item) {
                $dateTimeItem = $this->timezone->date(new \DateTime($item->getData('added_at')))->format('Y/m/d');
                $checkDay = date_diff(date_create($todayDate), date_create($dateTimeItem));
                $checkDay = $checkDay->d;
                if (!in_array($item->getData('product_id'), $productId) && $checkDay == $numberCheckConfig && $item->getData('check_send_reminder') != true) {
                    array_push($productId, $item->getData('product_id'));
                    $item->setData('check_send_reminder', true);
                    $item->save();
                }elseif(in_array($item->getData('product_id'), $productId) && $checkDay == $numberCheckConfig && $item->getData('check_send_reminder') != true)
                {
                    $item->setData('check_send_reminder', true);
                    $item->save();
                }
            }
        }
        $wishListId = $this->collectionFactory->create()->addFieldToFilter('customer_id', $customerId)->getFirstItem()->getWishlistId();
        if (isset($wishListId)) {
            $wishListCollectionFactory = $this->itemCollectionFactory->create()->addFieldToFilter('wishlist_id', $wishListId);
            foreach ($wishListCollectionFactory as $item) {
                $dateTimeItem = $this->timezone->date(new \DateTime($item->getData('added_at')))->format('Y/m/d');
                $checkDay = date_diff(date_create($todayDate), date_create($dateTimeItem));
                $checkDay = $checkDay->d;
                if (!in_array($item->getData('product_id'), $productId) && $checkDay == $numberCheckConfig && $item->getData('check_send_reminder') != true) {
                    array_push($productId, $item->getData('product_id'));
                    $item->setData('check_send_reminder', true);
                    $item->save();
                }elseif(in_array($item->getData('product_id'), $productId) && $checkDay == $numberCheckConfig && $item->getData('check_send_reminder') != true)
                {
                    $item->setData('check_send_reminder', true);
                    $item->save();
                }
            }
        }
        $return = $this->productCollectionFactory->create()->addAttributeToFilter('entity_id', array('in' => $productId));
        return $return;
    }

	/**
	 * @param $customerId
	 * @return array|\Magento\Catalog\Model\ResourceModel\Product\Collection
	 * @throws \Magento\Framework\Exception\AlreadyExistsException
	 */
	public function getNotification($customerId)
    {
        $return = array();
        $dataIdProduct = array();
        $qtyCheck = $this->_scopeConfig->getValue('multiplewishlist/cronsendmail/low_threshold',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $wishlistId = $this->collectionFactory->create()->addFieldToFilter('customer_id',$customerId)->getFirstItem()->getId();
        $wishlist = $this->wishlistFactory->create()->load($wishlistId);
        $notification = $wishlist->getNotification();
        if($notification!=null){
            $data = $this->serializer->unserialize($notification);
            if(count($data)>0){
                $dataIdProduct = $data;
                foreach ($data as $item) {
                    $product = $this->productModelFactory->create()->load($item);
                    if($product->getId())
                    {
                        if ($product->getTypeId() == "configurable" || $product->getTypeId() == 'grouped' || $product->getTypeId() == 'bundle') {
                            switch ($product->getTypeId()) {
                                case "configurable":
                                    $children = $product->getTypeInstance()->getUsedProducts($product);
                                    break;
                                case "grouped":
                                    $children = $product->getTypeInstance()->getAssociatedProducts($product);
                                    break;
                                case "bundle":
                                    $children = [];
                                    $selectionCollection = $product->getTypeInstance()
                                        ->getSelectionsCollection(
                                            $product->getTypeInstance()->getOptionsIds($product),
                                            $product
                                        );
                                    foreach ($selectionCollection as $productItem) {
                                        $children[] = $productItem;
                                    }
                                    break;
                            }

                            foreach ($children as $child){
                                if($this->checkModule()!=false)
                                {
                                    $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                                    $getSalableQuantityDataBySku  = $objectManager->create('Magento\InventorySalesAdminUi\Model\GetSalableQuantityDataBySku');
                                    $qtyData = $getSalableQuantityDataBySku->execute($child->getSku());
                                    $quantity = array_shift($qtyData);
                                    $qty = $quantity["qty"];
                                }else{
                                    $qty = $this->_stockItemRepository->get($child->getID())->getQty();
                                }
                                if ($qty > $qtyCheck) {
                                    if (($key = array_search($item, $data)) !== false) {
                                        unset($data[$key]);
                                    }
                                }
                            }
                        }else{
                            if($this->checkModule()!=false)
                            {
                                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                                $getSalableQuantityDataBySku  = $objectManager->create('Magento\InventorySalesAdminUi\Model\GetSalableQuantityDataBySku');
                                $qtyData = $getSalableQuantityDataBySku->execute($product->getSku());
                                $quantity = array_shift($qtyData);
                                $qty = $quantity["qty"];
                            }else{
                                $qty = $this->_stockItemRepository->get($product->getID())->getQty();
                            }
                            if ($qty > $qtyCheck) {
                                if (($key = array_search($item, $data)) !== false) {
                                    unset($data[$key]);
                                }
                            }
                        }
                    }
                }
            }
            foreach ($dataIdProduct as $value)
            {
                if (($key = array_search($value, $data)) !== false) {
                    unset($dataIdProduct[$key]);
                }
            }
            if(count($dataIdProduct)>0)
            {
                $dataIdProduct = $this->serializer->serialize($dataIdProduct);
                $wishlist->setNotification($dataIdProduct);
                $this->resourceWishList->save($wishlist);
            }else
            {
                $wishlist->setNotification(null);
                $this->resourceWishList->save($wishlist);
            }
            $return = $this->productCollectionFactory->create()->addAttributeToFilter('entity_id', array('in' => $data));
            return $return;
		}else return $return;
    }
    /**
     * Send mail for customer
     * @param $customerId
     * @param $data
     * @param $templateIdentifier
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\MailException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function sendMail($customerMail, $data, $templateIdentifier)
    {
        $store = $this->_storeManager->getStore()->getId();
        $transport = $this->transportBuilder->setTemplateIdentifier($templateIdentifier)
            ->setTemplateOptions(['area' => 'frontend', 'store' => $store])
            ->setTemplateVars($data)
            ->setFrom('general')
            // you can config general email address in Store -> Configuration -> General -> Store Email Addresses
            ->addTo($customerMail, 'Customer Name')
            ->getTransport();
        $transport->sendMessage();
    }

	/**
	 * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
	 */
	public function getProductCollectionVisibility()
    {
        $collection = $this->productCollectionFactory->create();
        $collection->addAttributeToFilter('status', ['in' => $this->productStatus->getVisibleStatusIds()]);
        $collection->setVisibility($this->productVisibility->getVisibleInSiteIds());
        return $collection;
    }

	/**
	 * @return mixed
	 */
	public function getWishlistId()
    {
        $data = $this->_getRequest()->getParams();
        $wishListId = $data['wishlist'];
        return $wishListId;
    }

	/**
	 * @param $wishlistId
	 * @return array
	 * @throws \Magento\Framework\Exception\NoSuchEntityException
	 */
	public function getWishlistItems($wishlistId)
    {
        $array = array();
        $pr = array();
        if($wishlistId != 'main')
        {
            $wl = $this->_multipleWishlistFactory->create()->load($wishlistId);
            $nameWishList = $wl->getName();
            $items = $wl->getProductsSharing();
            $firstItem = array_shift($items);
            $array['name'] = $nameWishList;
            $array['firstItem'] = $firstItem;
            return $array;
        }else
        {
            $wishListMainId = $this->_getRequest()->getParam('wishListMainId');
            $items = $this->itemCollectionFactory->create()->addFieldToFilter('wishlist_id', $wishListMainId)->getData();
            $firstItem = array_shift($items);
            $product = $this->productModelFactory->create()->load($firstItem['product_id']);
            $image = $product->getImage();
            $store = $this->_storeManager->getStore();
            if($image=="no_selection" || $image==null)
            {
                $pr['img_link']=$this->imageHelperFactory->create()->getDefaultPlaceholderUrl('image');
            }else{
                $pr['img_link'] = $store->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'catalog/product' . $product->getImage();
            }
            $array['name'] = 'Main';
            $array['firstItem']['img_link'] = $pr['img_link'];
            return $array;
        }
    }

	/**
	 * @param $product
	 * @return array
	 * @throws \Magento\Framework\Exception\NoSuchEntityException
	 */
	public function getProductSendMail($product)
    {
        $pr = array();
        if($product->getId()){
            if (!$product->getRatingSummary()) {
                $this->reviewFactory->create()->getEntitySummary($product, $this->_storeManager->getStore()->getId());
            }
            $image = $product->getImage();
            $pr['id'] = $product->getEntityId();
            $pr['product_url'] = $product->getProductUrl();
			$pr['price'] = $this->renderPrice($product);
            if($image=="no_selection" || $image==null)
            {
                $pr['img_link']=$this->imageHelperFactory->create()->getDefaultPlaceholderUrl('image');
            }else{
                $pr['img_link'] = $this->imageHelperFactory->create()->init($product, 'product_page_image_small')
                    ->setImageFile($product->getSmallImage())
                    ->resize(200)
                    ->getUrl();
            }
            $pr['product_name'] = $product->getName();
            $rating = $product->getRatingSummary()->getRatingSummary();
            if(isset($rating)){
                $pr['ratings'] = $rating ;
            }else $pr['ratings'] = null;
        }
        return $pr ;
    }

    public function checkModule()
    {
        if ($this->moduleManager->isEnabled('Magento_InventoryAdminUi')) {
            return true;
        } else {
            return false;
        }
    }

    public function renderPrice(\Magento\Catalog\Model\Product $product){
		$objectManager = ObjectManager::getInstance();
		/** @var \Magento\Framework\Pricing\Render $priceRender */
		$priceRender = $objectManager->create(Template::class)->getLayout()->getBlock('product.price.render.default');
		if (!$priceRender) {
			$priceRender = $objectManager->create(Template::class)->getLayout()->createBlock(
				\Magento\Framework\Pricing\Render::class,
				'product.price.render.default',
				['data' => ['price_render_handle' => 'catalog_product_prices']]
			);
		}

		$price = '';
		if ($priceRender) {
			$price = $priceRender->render(
				\Magento\Catalog\Pricing\Price\FinalPrice::PRICE_CODE,
				$product,
				[
					'display_minimal_price'  => true,
					'use_link_for_as_low_as' => true,
					'zone'                   => \Magento\Framework\Pricing\Render::ZONE_ITEM_LIST
				]
			);
		}
		return $price;
	}

    public function checkBeforeDelete($wishlistId,$productId,$customerId)
    {
        $return = true;
        $arrayWishListId = array();
        $item = array();
        $multipleWishlistCollection = $this->multipleWishlistCollection->create()->addFieldToFilter('customer_id', $customerId)->getItems();
        $wishlistIdMain = $this->collectionFactory->create()->addFieldToFilter('customer_id', $customerId)->getFirstItem()->getWishlistId();
        if (count($multipleWishlistCollection)) {
            foreach ($multipleWishlistCollection as $key => $value) {
                $arrayWishListId[] = $key;
            }
        }
        if($wishlistId!=null){
            if(in_array($wishlistId, $arrayWishListId)){
                $key = array_search($wishlistId, $arrayWishListId);
                unset($arrayWishListId[$key]);
            }
        }
        $allItem = $this->itemMultipleWishlistCollection->create()->addFieldToFilter('wishlist_id',array('in'=>$arrayWishListId))->getData();
        foreach ($allItem as $value)
        {
            if($value['product_id']==$productId)
            {
                $return = false;
                break;
            }
        }
        if($wishlistId!=null){
            if (isset($wishlistIdMain)) {
                $wishListCollectionFactory = $this->itemCollectionFactory->create()->addFieldToFilter('wishlist_id', $wishlistIdMain);
                foreach ($wishListCollectionFactory as $value) {
                    if($value['product_id']==$productId)
                    {
                        $return = false;
                        break;
                    }
                }
            }
        }
        return $return;
    }
}
