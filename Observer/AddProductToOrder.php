<?php

namespace Magenest\MultipleWishlist\Observer;

use Magento\Wishlist\Model\Item;

/**
 * Class AddProductToOrder
 * @package Magenest\MultipleWishlist\Observer
 */
class AddProductToOrder implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $productCollectionFactory;
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product
     */
    protected $productResource;
    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;
    /**
     * @var \Magenest\MultipleWishlist\Model\ReportWishlistFactory
     */
    protected $reportFactory;
    /**
     * @var \Magenest\MultipleWishlist\Model\ResourceModel\ReportWishlist
     */
    protected $reportResource;
    /**
     * @var \Magento\Catalog\Model\ProductRepository
     */
    protected $productRepository;
    /**
     * @var \Magento\CatalogInventory\Api\StockRegistryInterface
     */
    protected $stockRegistry;
    /**
     * @var \Magenest\MultipleWishlist\Model\ResourceModel\ReportWishlist\CollectionFactory
     */
    protected $reportCollectionFactoty;
    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $date;
    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $orderFactory;

    protected $collectionWishList;

    protected $collectionWishListItem;

    protected $modelWishListItem;

    protected $customerSession;

    protected $collectionWishListMagenest;

    protected $collectionItemMagenest;

    protected $itemFactory;

    protected $serializer;

    protected $wishlistFactory;

    protected $resourceWishList;
    /**
     * AddProductToOrder constructor.
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Catalog\Model\ResourceModel\Product $productResource
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magenest\MultipleWishlist\Model\ReportWishlistFactory $reportFactory
     * @param \Magenest\MultipleWishlist\Model\ResourceModel\ReportWishlist\CollectionFactory $reportCollectionFactoty
     * @param \Magenest\MultipleWishlist\Model\ResourceModel\ReportWishlist $reportResource
     * @param \Magento\Catalog\Model\ProductRepository $productRepository
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\ResourceModel\Product $productResource,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magenest\MultipleWishlist\Model\ReportWishlistFactory $reportFactory,
        \Magenest\MultipleWishlist\Model\ResourceModel\ReportWishlist\CollectionFactory $reportCollectionFactoty,
        \Magenest\MultipleWishlist\Model\ResourceModel\ReportWishlist $reportResource,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Wishlist\Model\ResourceModel\Wishlist\CollectionFactory $collectionWishList,
        \Magento\Wishlist\Model\ResourceModel\Item\CollectionFactory $collectionWishListItem,
        \Magento\Wishlist\Model\ItemFactory $modelWishListItem,
        \Magento\Customer\Model\Session $customerSession,
        \Magenest\MultipleWishlist\Model\ResourceModel\MultipleWishlist\CollectionFactory $collectionWishListMagenest,
        \Magenest\MultipleWishlist\Model\ResourceModel\Item\CollectionFactory $collectionItemMagenest,
        \Magenest\MultipleWishlist\Model\ItemFactory $itemFactory,
        \Magento\Framework\Serialize\Serializer\Json $serializer,
        \Magento\Wishlist\Model\WishlistFactory $wishlistFactory,
        \Magento\Wishlist\Model\ResourceModel\Wishlist $resourceWishList
    )
    {
        $this->logger = $logger;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->productResource = $productResource;
        $this->productFactory = $productFactory;
        $this->reportFactory = $reportFactory;
        $this->reportResource = $reportResource;
        $this->productRepository = $productRepository;
        $this->stockRegistry = $stockRegistry;
        $this->reportCollectionFactoty = $reportCollectionFactoty;
        $this->date = $date;
        $this->orderFactory = $orderFactory;
        $this->collectionWishList = $collectionWishList;
        $this->collectionWishListItem = $collectionWishListItem;
        $this->customerSession = $customerSession;
        $this->modelWishListItem = $modelWishListItem;
        $this->collectionWishListMagenest = $collectionWishListMagenest;
        $this->collectionItemMagenest = $collectionItemMagenest;
        $this->itemFactory = $itemFactory;
        $this->serializer = $serializer;
        $this->wishlistFactory = $wishlistFactory;
        $this->resourceWishList = $resourceWishList;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        $items = $order->getAllVisibleItems();
        $productId = array();
        $qtyProduct = array();
        foreach ($items as $item):{
            $data = $item->getData();
            $productId[] = $data['product_id'];
            $qtyProduct[$data['product_id']] = $data['qty_ordered'];
        }endforeach;
        $collection = $this->reportCollectionFactoty->create()->addFieldToFilter('product_id', array('in' => $productId));
        $data = $collection->getData();
        foreach ($data as $item):{
            $entity_id = $item['entity_id'];
            $tmp = $this->reportFactory->create()->load($entity_id);
            $addOrder = $tmp->getData('addOrder');
            $count = $tmp->getData('count');
            if ($addOrder < $count) {
                $tmp->setData('addOrder', $addOrder + $qtyProduct[$item['product_id']]);
            }
            $this->reportResource->save($tmp);
        }endforeach;
        $customerSession = $this->customerSession;
        if ($customerSession->isLoggedIn()) {
            $Datawishlist = $this->collectionWishList->create()->addFieldToFilter('customer_id', $customerSession->getCustomer()->getId())->getData();
            $collectionWishList = $this->collectionWishListMagenest->create()->addFieldToFilter('customer_id', $customerSession->getCustomer()->getId())->getData();
            foreach ($productId as $value) {
                $Item = $this->collectionWishListItem->create()->addFieldToFilter('wishlist_id',$Datawishlist[0]['wishlist_id'])->addFieldToFilter('product_id', $value)->getData();
                if ($Item && ($Item[0]['qty'] <= $qtyProduct[$Item[0]['product_id']])) {
                    $this->modelWishListItem->create()->load($Item[0]['wishlist_item_id'])->delete();
                }
            }
            foreach ($collectionWishList as $data)
            {
                foreach ($productId as $value) {
                    $Item = $this->collectionItemMagenest->create()->addFieldToFilter('wishlist_id',$data['id'])->addFieldToFilter('product_id', $value)->getData();
                    if ($Item && ($Item[0]['qty'] <= $qtyProduct[$Item[0]['product_id']])) {
                        $this->itemFactory->create()->load($Item[0]['id'])->delete();
                    }
                }
            }
            $wishlistCollection = $this->collectionWishList->create()->addFieldToFilter('customer_id',$customerSession->getCustomer()->getId())->getData();
            $wishlist = $this->wishlistFactory->create()->load($wishlistCollection['0']['wishlist_id']);
            if($wishlist->getNotification()!=null) {
                $notification = $this->serializer->unserialize($wishlist->getNotification());
                foreach ($productId as $item) {
                    if (in_array($item, $notification, true)) {
                        if (($key = array_search($item, $notification)) !== false) {
                            unset($notification[$key]);
                        }
                        $wishlist->setNotification($this->serializer->serialize($notification));
                        $this->resourceWishList->save($wishlist);
                    }
                }
            }
        }
    }
}