<?php

namespace Magenest\MultipleWishlist\Observer;
/**
 * Class AddProductToReportWishlist
 * @package Magenest\MultipleWishlist\Observer
 */
class AddProductToReportWishlist implements \Magento\Framework\Event\ObserverInterface
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
    protected $ReportCollectionFactoty;
    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $date;

    protected $serializer;

    protected $wishlistFactory;

    protected $customerSession;

    protected $resourceWishList;

    protected $collectionFactory;
    /**
     * AddProductToReportWishlist constructor.
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Catalog\Model\ResourceModel\Product $productResource
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magenest\MultipleWishlist\Model\ReportWishlistFactory $reportFactory
     * @param \Magenest\MultipleWishlist\Model\ResourceModel\ReportWishlist\CollectionFactory $ReportCollectionFactoty
     * @param \Magenest\MultipleWishlist\Model\ResourceModel\ReportWishlist $reportResource
     * @param \Magento\Catalog\Model\ProductRepository $productRepository
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\ResourceModel\Product $productResource,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magenest\MultipleWishlist\Model\ReportWishlistFactory $reportFactory,
        \Magenest\MultipleWishlist\Model\ResourceModel\ReportWishlist\CollectionFactory $ReportCollectionFactoty,
        \Magenest\MultipleWishlist\Model\ResourceModel\ReportWishlist $reportResource,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\Serialize\Serializer\Json $serializer,
        \Magento\Wishlist\Model\WishlistFactory $wishlistFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Wishlist\Model\ResourceModel\Wishlist $resourceWishList,
        \Magento\Wishlist\Model\ResourceModel\Wishlist\CollectionFactory $collectionFactory
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
        $this->ReportCollectionFactoty = $ReportCollectionFactoty;
        $this->date = $date;
        $this->serializer = $serializer;
        $this->wishlistFactory = $wishlistFactory;
        $this->customerSession = $customerSession;
        $this->resourceWishList = $resourceWishList;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $productId = $observer->getModel();
        if (isset($productId)) {
            $check = $this->ReportCollectionFactoty->create()->addFieldToFilter('product_id', $productId)->getData();
            if ($check != null) {
                $productReportId = $check[0]['entity_id'];
                $tmp = $this->reportFactory->create()->load($productReportId);
                $stock = $this->stockRegistry->getStockItem($productId)->getData();
                $date = $this->date->gmtDate();
                $count = $tmp->getCount();
                $tmp->setData('count', $count + 1);
                $tmp->setData('stockQty', $stock['qty']);
                $tmp->setData('last_added', $date);
                $this->reportResource->save($tmp);
            } else {
                $data = $this->productRepository->getById($productId)->getData();
                $stock = $this->stockRegistry->getStockItem($productId)->getData();
                $modelReport = $this->reportFactory->create();
                $modelReport->setData('product_id', $productId);
                $modelReport->setData('name', $data['name']);
                $modelReport->setData('sku', $data['sku']);
                $modelReport->setData('typeProduct', $data['type_id']);
                $modelReport->setData('stockQty', $stock['qty']);
                $modelReport->setData('count', 1);
                $this->reportResource->save($modelReport);
            }
            $customerSession = $this->customerSession;
            if ($customerSession->isLoggedIn()) {
                $wishlistCollection = $this->collectionFactory->create()->addFieldToFilter('customer_id',$customerSession->getCustomer()->getId())->getData();
                $wishlist = $this->wishlistFactory->create()->load($wishlistCollection['0']['wishlist_id']);
                if($wishlist->getNotification()!=null){
                    $notification = $this->serializer->unserialize($wishlist->getNotification());
                    if(!in_array((int)$productId,$notification,true))
                    {
                        array_push($notification,(int)$productId);
                        $wishlist->setNotification($this->serializer->serialize($notification));
                        $this->resourceWishList->save($wishlist);
                    }
                }else{
                    $return = array();
                    array_push($return,(int)$productId);
                    $wishlist->setNotification($this->serializer->serialize($return));
                    $this->resourceWishList->save($wishlist);
                }
            }
        }
    }
}