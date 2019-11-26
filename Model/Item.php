<?php

namespace Magenest\MultipleWishlist\Model;

use Magento\Wishlist\Controller\WishlistProvider;

class Item extends \Magento\Framework\Model\AbstractModel
{

    /**
     * @var $_store \Magento\Store\Api\Data\StoreInterface
     */

    protected $_store;

    protected $_eventPrefix = 'multiplewishlist_item';

    protected $productRepository;

    protected $helper;

    protected $_storeId;

    protected $wishlistProvider;

    protected $productFactory;

    protected $optionCollectionFactory;

    protected $optionFactory;

    protected $optionResource;

    protected $itemCollectionFactory;

    protected $mutilpleWishlistFactory;

    protected $mutilpleWishlistResourece;

    protected $cartFactory;

    protected $itemFactory;

    protected $itemResource;

    private $serializerinterface;

    protected $imageHelperFactory;

    protected $itemWishListFactory;

	protected $wishlistFactory;

	protected $serializer;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
//         \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magenest\MultipleWishlist\Model\ResourceModel\Item $resource,
        \Magenest\MultipleWishlist\Model\ResourceModel\Item\Collection $resourceCollection,
        \Magenest\MultipleWishlist\Helper\Data $helper,
        WishlistProvider $wishlistProvider,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepositoryInterface,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magenest\MultipleWishlist\Model\ResourceModel\Item\Option\CollectionFactory $optionCollectionFactory,
        \Magenest\MultipleWishlist\Model\ResourceModel\Item\CollectionFactory $itemCollectionFactory,
        \Magenest\MultipleWishlist\Model\MultipleWishlistFactory $mutilpleWishlistFactory,
        \Magenest\MultipleWishlist\Model\ResourceModel\MultipleWishlist $mutilpleWishlistResourece,
        \Magenest\MultipleWishlist\Model\Item\OptionFactory $optionFactory,
        \Magento\Checkout\Model\CartFactory $cartFactory,
        \Magenest\MultipleWishlist\Model\ItemFactory $itemFactory,
        \Magenest\MultipleWishlist\Model\ResourceModel\Item $itemResource,
        \Magento\Framework\Serialize\SerializerInterface $serializer,
        \Magenest\MultipleWishlist\Model\ResourceModel\Item\Option $optionResource,
        \Magento\Catalog\Helper\ImageFactory $imageHelperFactory,
        \Magento\Wishlist\Model\ItemFactory $itemWishListFactory,
		\Magento\Wishlist\Model\WishlistFactory $wishlistFactory,
        array $data = []
    )
    {
        $this->_store = $storeManager->getStore();
        $this->_storeId = $this->_store->getId();
        $this->wishlistProvider = $wishlistProvider;
        $this->_init('Magenest\MultipleWishlist\Model\ResourceModel\Item');
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->productRepository = $productRepositoryInterface;
        $this->helper = $helper;
        $this->productFactory = $productFactory;
        $this->optionCollectionFactory = $optionCollectionFactory;
        $this->itemCollectionFactory = $itemCollectionFactory;
        $this->mutilpleWishlistFactory = $mutilpleWishlistFactory;
        $this->optionFactory = $optionFactory;
        $this->optionResource = $optionResource;
        $this->cartFactory = $cartFactory;
        $this->itemFactory = $itemFactory;
        $this->serializerinterface = $serializer;
        $this->itemResource = $itemResource;
        $this->mutilpleWishlistResourece = $mutilpleWishlistResourece;
        $this->imageHelperFactory = $imageHelperFactory;
        $this->itemWishListFactory = $itemWishListFactory;
        $this->wishlistFactory = $wishlistFactory;
    }

    public function getItems($wishlistId)
    {
        $return = array();
        $items = $this->getItemsByWishlist($wishlistId);

        foreach ($items as $item) {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

            $products = $this->productFactory->create();
            $options = $this->optionCollectionFactory->create();

            $pr = array();
            /**
             * @var $product \Magento\Catalog\Model\Product
             * @var $optionsItem Magenest\MultipleWishlist\Model\ResourceModel\Item\Option\Collection
             */
            $product = $products->load($item['product_id']);
            if($product->getId()){
            $image = $product->getImage();
            $listOptions = $product->getExtensionAttributes()->getConfigurableProductOptions();
            /* if ($product->getData()['has_options'] === '1') {
                 $optionsItem = $options->load($item['wishlist_id']);
                 $sendOptions = [];
                 foreach ($optionsItem as $optionItem){
                     $code = $optionItem->getDataByKey('code');
                     if ($code === 'attributes' && $item['wishlist_id'] == $optionItem->getDataByKey('multiplewishlist_item_id')) {
                         $customerOption = $optionItem->getDataByKey('value');
                         $data = json_decode($customerOption,true);          //moisua
                         foreach ($data as $key => $value) {
                             foreach ($listOptions as $optionData){
                                 $attributeId = $optionData->getAttributeId();
                                 $label = $optionData->getLabel();
                                 $detailOption = $optionData->getOptions();
                                 if ($key == $attributeId){
                                     foreach ($detailOption as $op) {
                                         if ($value == $op['value_index']){
                                             $sendOption = [$label => $op['label']];
                                             array_push($sendOptions, $sendOption);
                                         }
                                     }
                                 }
                             }
                         }
                     }
                 }
                 $pr['customer_options'] = $sendOptions;
             }*/

            $pr['product_url'] = $product->getProductUrl();
            $pr['price'] = $this->helper->renderPrice($product);
            if($image=="no_selection" || $image==null)
            {
                $pr['img_link']=$this->imageHelperFactory->create()->getDefaultPlaceholderUrl('image');
            }else{
                $pr['img_link'] = $this->_store->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'catalog/product' . $product->getImage();
            }
            $pr['product_name'] = $product->getName();
            $pr['qty'] = $item['qty'];
            $pr['id'] = $item['id'];
            $pr['description'] = $item['description'];
            $pr['product'] = $product;
            array_push($return, $pr);
            }
        }

        return $return;
    }

    public function getItemsByWishlist($wishlistId)
    {
        $itemCollection = $this->itemCollectionFactory->create()
            ->addFieldToFilter('store_id', $this->_storeId)
            ->addFieldToFilter('wishlist_id', $wishlistId);
        return $itemCollection->getData();
    }

    public function getProduct()
    {
        $productId = $this->getData('product_id');

        $product = $this->productRepository->getById($productId);

        $customOptionsArr = $this->helper->getCustomOptionAsArr($this->getId());
        $product->setCustomOptions($customOptionsArr);
        return $product;

    }

    public function deleteByWishlist($wishlistId)
    {
        $items = $this->getItemsByWishlist($wishlistId);
        $mutilpleWishlist = $this->mutilpleWishlistFactory->create()->load($wishlistId);
        $customerId = $mutilpleWishlist->getCustomerId();
		$wishlist = $this->wishlistFactory->create()->load($customerId, 'customer_id');
		if (!$wishlist->getWislistId()) $notification = $wishlist->getNotification();
        if($notification != null)
        {
            $notification = $this->serializerinterface->unserialize($notification);
        }
		foreach ($items as $item) {
        	$productId = $item['product_id'];
				if ($notification != null && count($notification)>0) {
                    $checkBeforeDelete = $this->helper->checkBeforeDelete($wishlistId,$productId,$customerId);
                    if (in_array($productId, $notification) && $checkBeforeDelete != false) {
						$key = array_search($productId, $notification);
						unset($notification[$key]);
					}
			}
            $this->load($item['id'])->delete();
        }
        $wishlist->setNotification($this->serializerinterface->serialize($notification));
        $wishlist->save();
    }

    public function delete()
    {
        if ($this->isOwner()) {
            // TODO: delete option
            parent::delete();
        }
    }

    /**
     * @return bool
     */
    public function isOwner()
    {
        return $this->getWishlist()->isOwner();
    }

    /**
     * @return MultipleWishlist
     */
    public function getWishlist()
    {
        $wishlistId = $this->getData('wishlist_id');
        /** @var \Magenest\MultipleWishlist\Model\MultipleWishlist $wishlist */
        $wishlist = $this->mutilpleWishlistFactory->create()->load($wishlistId);
        return $wishlist;
    }

    public function moveTo($wishlistId)
    {
        if (!$this->isOwner()) return false;

        $productId = $this->getData('product_id');

        /**
         * @var $itemFactory \Magenest\MultipleWishlist\Model\Item
         */


        /**
         * @var $itemCollection \Magenest\MultipleWishlist\Model\ResourceModel\Item\Collection
         */
        $itemFactory = $this->itemFactory->create();
        $itemCollection = $this->itemCollectionFactory->create()
            ->addFieldToFilter('store_id', $this->_storeId)
            ->addFieldToFilter('wishlist_id', $wishlistId)
            ->addFieldToFilter('product_id', $productId);

        $itemDatas = $itemCollection->getData();

        /**
         * @var $optionFactory \Magenest\MultipleWishlist\Model\Item\Option
         */
        $optionFactory = $this->optionFactory->create();

        $optionCollection = $this->optionCollectionFactory->create()
            ->addFieldToFilter('multiplewishlist_item_id', $this->getId());

        $attributes = $this->getOptionValue('attributes');

        $idFieldName = $this->getIdFieldName();
        $isAdded = false;

        foreach ($itemDatas as $itemData) {
            /*  $item = $itemFactory->load($itemData[$idFieldName]);
            if ($attributes == $item->getOptionValue('attributes')) {

                $qty = $this->getData('qty');
                $itemData['qty'] = $itemData['qty'] + $qty;
                $itemFactory->setData($itemData);
                $this->itemResource->save($item);
                $this->delete();

                $infoBuyRequest = $item->getOptionValue('info_buyRequest');
                $infoBuyRequest['qty'] = $item['qty'];

                $tmpOption = $this->loadOption('info_buyRequest');
                $tmpOption->setData($infoBuyRequest);
                $this->optionResource->save($tmpOption);

                $isAdded = true;
            }*/
            $isAdded = true;
            return false;
        }

        if (!$isAdded) {
            $qty = $this->getData('qty');
            $itemFactory->setData('wishlist_id', $wishlistId);
            $itemFactory->setData('product_id', $productId);
            $itemFactory->setData('store_id', $this->_storeId);
            $itemFactory->setData('qty', $qty);
            $this->itemResource->save($itemFactory);
            $this->delete();
            return true;
        }
    }

    /**
     * @param string $code
     * @return bool|mixed
     */
    public function getOptionValue($code)
    {
        $return = false;

        /*** @var $optionFactory \Magenest\MultipleWishlist\Model\Item\Option */
        $optionFactory = $this->optionFactory->create();

        $optionCollection = $this->optionCollectionFactory->create()
            ->addFieldToFilter('multiplewishlist_item_id', $this->getId());

        $options = $optionCollection->getData();

        foreach ($options as $option) {
            if ($option['code'] == $code) {
                $return = $this->serializerinterface->unserialize($option['value']);
            }
        }

        return $return;
    }

    /**
     * @param string $code
     * @return $this|bool
     */
    public function loadOption($code)
    {
        $return = false;

        /*** @var $optionFactory \Magenest\MultipleWishlist\Model\Item\Option */
        $optionFactory = $this->optionFactory->create();

        $optionCollection = $this->optionCollectionFactory->create()
            ->addFieldToFilter('multiplewishlist_item_id', $this->getId());

        $options = $optionCollection->getData();

        $idFieldName = $optionFactory->getIdFieldName();

        foreach ($options as $option) {
            if ($option['code'] == $code) {
                $return = $optionFactory->load($option[$idFieldName]);
            }
        }

        return $return;
    }

    public function copyTo($wishlistId)
    {
        $superAttribute = [];
        $productId = $this->getProductId();
        $qty = $this->getData('qty');
        $buyRequestArr['product'] = $productId;
        /*$buyRequest = $this->getOptionValue('info_buyRequest');*/
        $buyRequestArr['super_attribute'] = $superAttribute;
        $addItem=$this->addItem($wishlistId, $productId, $qty, $buyRequestArr);
        return $addItem;
    }

    public function getProductId()
    {
        return $this->getData('product_id');
    }

    public function addItem($wishlistId, $productId, $qty = 1, $buyRequestArr = null)       /*noteeeeeeee*/
    {
        $product = $this->productRepository->getById($productId);

        $storeId = $product->hasWishlistStoreId() ? $product->getWishlistStoreId() : $product->getStoreId();

        $_buyRequest = new \Magento\Framework\DataObject($buyRequestArr);

        /* @var $product \Magento\Catalog\Model\Product */
        $cartCandidates = $product->getTypeInstance()->processConfiguration($_buyRequest, clone $product);

        /**
         * Error message
         */
        if (is_string($cartCandidates)) {
            return $cartCandidates;
        }

        /**
         * If prepare process return one object
         */
        if (!is_array($cartCandidates)) {
            $cartCandidates = [$cartCandidates];
        }

        /*** @var $optionFactory \Magenest\MultipleWishlist\Model\Item\Option */
        $optionFactory = $this->optionFactory->create();

//        $storeId = $this->_store->getId();

        $itemCollection = $this->itemCollectionFactory->create()
            ->addFieldToFilter('store_id', $this->_storeId)
            ->addFieldToFilter('wishlist_id', $wishlistId)
            ->addFieldToFilter('product_id', $productId);

        $items = $itemCollection->getData();

        if (!empty($items)) {
           return false;
        }
        if (empty($items)) {
            $items = array(
                'wishlist_id' => $wishlistId,
                'product_id' => $productId,
                'store_id' => $this->_storeId,
                'description' => '',
                'qty' => $qty,
            );
            /*   $this->itemResource->save($this->setData($items));*/
            $this->itemResource->save($this->setData($items));
            if (!empty($buyRequestArr)) {
                $itemId = $this->getId();
                $optionFactory->addOption($productId, $itemId, $cartCandidates, $storeId);
            }
        }
        return true;
    }

    public function moveToMain()
    {
        $isOwner = $this->isOwner();
        if (!$isOwner) return false;
        $productId = $this->getData('product_id');
        $qty = $this->getData('qty');

        $attributes = $this->getOptionValue('attributes');

        $buyRequest = !$attributes ? null : new \Magento\Framework\DataObject([
            'product' => $productId,
            'super_attribute' => $attributes,
            'qty' => '' . (int)$qty
        ]);

        $wishlist = $this->wishlistProvider->getWishlist();
        $wishlistResult = $wishlist->addNewItem($productId, $buyRequest);
//
        if (is_string($wishlistResult)) {
            return $wishlistResult;
        }

        $modelItemFactory = $this->itemWishListFactory->create()->load($wishlistResult->getData('wishlist_item_id'));
        $qty = $modelItemFactory->getData('qty');
        if($qty == 1){
            $wishlist->save();
            $this->delete();
            return true;
        }elseif ($qty > 1){
            $modelItemFactory->setQty(1);
            $modelItemFactory->save();
            $wishlist->save();
            return false;
        }
    }

    public function copyToMain()
    {
        $productId = $this->getData('product_id');
        $qty = 0;

        $attributes = $this->getOptionValue('attributes');

        $buyRequest = !$attributes ? null : new \Magento\Framework\DataObject([
            'product' => $productId,
            'super_attribute' => $attributes,
            'qty' => '' . (int)$qty
        ]);

        $wishlist = $this->wishlistProvider->getWishlist();
        $wishlistResult = $wishlist->addNewItem($productId, $buyRequest);
//
        if (is_string($wishlistResult)) {
            return $wishlistResult;
        }
        $modelItemFactory = $this->itemWishListFactory->create()->load($wishlistResult->getData('wishlist_item_id'));
        $qty = $modelItemFactory->getData('qty');
        if($qty == 1){
            $wishlist->save();
            return true;
        }elseif ($qty > 1){
            $modelItemFactory->setQty(1);
            $modelItemFactory->save();
            $wishlist->save();
            return false;
        }


    }

    public function addToCart($qty = null)
    {
        /** @var \Magento\Checkout\Model\Cart $cart */
        $cart = $this->cartFactory->create();

        $productId = $this->getProductId();

        $info_buyRequest = $this->loadOption('info_buyRequest');


        /* $initialData = $info_buyRequest ? json_decode($info_buyRequest->getData('value'),true) : null;*/
        if (!empty($qty)) $initialData['qty'] = (int)$qty;

        $buyRequest = $initialData ? new \Magento\Framework\DataObject($initialData) : null;

        try {
            $cart->addProduct($productId, $buyRequest);
//            $cart->save();
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            return $e->getMessage();
        }
    }

    public function getDescription()
    {
        return $this->getData('description');
    }

    public function setDescription($description)
    {
        return $this->itemResource->save($this->setData('description', $description));
    }

    public function setQty($qty)
    {
       /* $this->setData('qty', $qty);*/

        /*** @var $optionFactory \Magenest\MultipleWishlist\Model\Item\Option */
        $optionFactory = $this->optionFactory->create();

        $optionCollection = $this->optionCollectionFactory->create()
            ->addFieldToFilter('multiplewishlist_item_id', $this->getId());
        $options = $optionCollection->getData();

        foreach ($options as $option) {
            if ($option['code'] == 'info_buyRequest') {
                /*$data = $option['value'];*/
                /*  $optionValue =  $this->serializerinterface->unserialize($option['value']);*/
                $optionValue['qty'] = $qty;
                $unserializeOption['value'] = $optionValue;
                $option['value'] = $this->serializerinterface->serialize($unserializeOption['value']);
                /** @var $tmpOption \Magenest\MultipleWishlist\Model\Item\Option */
                $tmpOption = $this->optionFactory->create();
                $tmpOption->setData($option);
                $this->optionResource->save($tmpOption);
                /*$tmpOption->save();*/
                unset($tmpOption);
            }
        }

        return $this->itemResource->save($this->setData('qty', $qty));
    }

    public function getQty()
    {
        return $this->getData('qty');
    }
}
