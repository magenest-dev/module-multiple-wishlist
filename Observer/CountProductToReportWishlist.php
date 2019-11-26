<?php

namespace Magenest\MultipleWishlist\Observer;
/**
 * Class AddProductToReportWishlist
 * @package Magenest\MultipleWishlist\Observer
 */
class CountProductToReportWishlist implements \Magento\Framework\Event\ObserverInterface
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
    
    protected $itemFactory;

    protected $itemWishListFactory;
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
        \Magenest\MultipleWishlist\Model\ItemFactory $itemFactory,
        \Magento\Wishlist\Model\ItemFactory $itemWishListFactory
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
        $this->itemFactory = $itemFactory;
        $this->itemWishListFactory = $itemWishListFactory;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $itemData = $observer->getItem_id();
        $itemIdParsed = explode('-', $itemData);
        if ($itemIdParsed[0] == 'main') {
            $itemData = $itemIdParsed[1];
            $productId = $this->itemWishListFactory->create()->load($itemData)->getProduct_id();
        }else $productId = $this->itemFactory->create()->load($itemData)->getProduct_id();
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
        }
    }
}