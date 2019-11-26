<?php
/**
 * Created by PhpStorm.
 * User: chung
 * Date: 3/11/17
 * Time: 11:55 AM
 */

namespace Magenest\MultipleWishlist\Controller\Item;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Customer\Model\Session;

class Copy extends Action
{
    protected $_resultJsonFactory;
    protected $_session;
    protected $itemModelFactory;
    protected $multipleWishlistModelFactory;
    protected $itemMagentoModelFactory;
    protected $date;
    protected $reportFactory;
    protected $reportCollectionFactoty;
    protected $reportResource;
    protected $stockRegistry;
    protected $itemFactory;
    protected $serializer;
    protected $wishlistFactory;
    protected $resourceWishList;
    protected $customerSession;
    protected $collectionFactory;
    protected $serializerInterface;

    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        Session $session,
        \Magenest\MultipleWishlist\Model\ItemFactory $itemModelFactory,
        \Magenest\MultipleWishlist\Model\MultipleWishlistFactory $multipleWishlistModelFactory,
        \Magento\Wishlist\Model\ItemFactory $itemMagentoModelFactory,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magenest\MultipleWishlist\Model\ReportWishlistFactory $reportFactory,
        \Magenest\MultipleWishlist\Model\ResourceModel\ReportWishlist\CollectionFactory $reportCollectionFactoty,
        \Magenest\MultipleWishlist\Model\ResourceModel\ReportWishlist $reportResource,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magenest\MultipleWishlist\Model\ItemFactory $itemFactory,
        \Magento\Framework\Serialize\Serializer\Json $serializer,
        \Magento\Wishlist\Model\WishlistFactory $wishlistFactory,
        \Magento\Wishlist\Model\ResourceModel\Wishlist $resourceWishList,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Wishlist\Model\ResourceModel\Wishlist\CollectionFactory $collectionFactory,
        \Magento\Framework\Serialize\SerializerInterface $serializerInterface
    )
    {
        $this->_resultJsonFactory = $jsonFactory;
        $this->_session = $session;
        $this->itemModelFactory = $itemModelFactory;
        $this->multipleWishlistModelFactory = $multipleWishlistModelFactory;
        $this->itemMagentoModelFactory = $itemMagentoModelFactory;
        $this->date = $date;
        $this->reportFactory = $reportFactory;
        $this->reportResource = $reportResource;
        $this->reportCollectionFactoty = $reportCollectionFactoty;
        $this->stockRegistry = $stockRegistry;
        $this->itemFactory = $itemFactory;
        $this->serializer = $serializer;
        $this->wishlistFactory = $wishlistFactory;
        $this->resourceWishList = $resourceWishList;
        $this->customerSession = $customerSession;
        $this->collectionFactory = $collectionFactory;
        $this->serializerInterface = $serializerInterface;
        parent::__construct($context);
    }

    public function execute()
    {
        $return = $this->_resultJsonFactory->create()->setData(['success' => false]);

        if (!$this->_session->isLoggedIn()) {
            $return->setData(['detail' => 'notLogin']);
            $this->messageManager->addErrorMessage('You are not logged in');
            return $return;
        }

        /** @var \Magenest\MultipleWishlist\Model\Item $itemFactory */
        $itemFactory = $this->itemModelFactory->create();

        $requestParams = $this->getRequest()->getParams();

        $wishlistId = isset($requestParams['wishlist']) ? $requestParams['wishlist'] : null;
        $itemId = isset($requestParams['itemId']) ? $requestParams['itemId'] : null;
//        $newName = isset($requestParams['newName']) ? $requestParams['newName'] : null;
        $this->_eventManager->dispatch('Count_Product_to_ReportWishlist', ['item_id' => $itemId]);
        if (isset($itemId)) {
            $itemIdParsed = explode('-', $itemId);
            if ($itemIdParsed[0] == 'main') {
                if ($wishlistId == 'main')
                {
                    $this->messageManager->addNoticeMessage('Product has already existed');
                    return $this->_redirect('wishlist',array('wishlistId'=>0));
                }
                if ($wishlistId == 'new') {
                    /**
                     * @var $wishlist \Magenest\MultipleWishlist\Model\MultipleWishlist
                     */
                    $wishlist = $this->multipleWishlistModelFactory->create();

                    $newWishlistName = !empty($requestParams['newName']) ? $requestParams['newName'] : 'default name';

                    $customerId = $this->_session->getCustomerId();

                    $wishlistId = $wishlist->addNewWishlist($customerId, $newWishlistName);
                }
                // Copy from main
                $itemTmp = $itemId;
                $itemId = $itemIdParsed[1];
                /** @var \Magento\Wishlist\Model\Item $itemCoreF */
                $itemCoreF = $this->itemMagentoModelFactory->create();
                $itemCore = $itemCoreF->loadWithOptions($itemId);

                $productId = $itemCore->getProductId();
                $qty = $itemCore->getData('qty');

                $buyRequestArr = [];
                $buyRequestArr['product'] = $productId;

                $options = $itemCore->getOptionsByCode();
                $superAttribute = [];
                /** @var \Magento\Wishlist\Model\Item\Option $option */
                foreach ($options as $option) {
                    $optionData = $option->getData();
                    if ($optionData['code'] == 'attributes') {
                        $superAttribute = $this->serializerInterface->unserialize($optionData['value']);
                    }
                }
                $buyRequestArr['super_attribute'] = $superAttribute;

                $value = $itemFactory->addItem($wishlistId, $productId, $qty, $buyRequestArr);
                if ($value) {
                    $this->messageManager->addSuccessMessage('Copied');
                    $this->countProductToReportWishlist($itemTmp);
                } else {
                    $this->messageManager->addNoticeMessage('Product has already existed');
                }
                return $this->_redirect('wishlist',array('wishlistId'=>$wishlistId));
            } else {
                /**
                 * @var $item \Magenest\MultipleWishlist\Model\Item
                 */
                $item = $itemFactory->load($itemId);
            }
        } else {
            $return->setData(['detail' => 'no item']);
            return $return;
        }

        if ($wishlistId === 'main') {

            $checkQty = $item->copyToMain();
            if ($checkQty) {
                $this->messageManager->addSuccessMessage('Copied');
                $this->countProductToReportWishlist($itemId);
            } else {
                $this->messageManager->addNoticeMessage('Product has already existed');
            }
            return $this->_redirect('wishlist',array('wishlistId'=>0));
        } elseif (is_numeric($wishlistId)) {
            $wishlistId = $wishlistId + 0;

            if (!is_int($wishlistId)) {
                /**
                 * @var $wishlist \Magenest\MultipleWishlist\Model\MultipleWishlist
                 */
                $mwlFactory = $this->multipleWishlistModelFactory->create();
                $wishlist = $mwlFactory->load($wishlistId);

                if (!$wishlist->isOwner()) {
                    $return->setData(['detail' => 'Can\'t specify wishlist.']);
                    return $this->_redirect('wishlist',array('wishlistId'=>$wishlistId));
                }
            }
            $value = $item->copyTo($wishlistId);
            if ($value) {
                $this->messageManager->addSuccessMessage('Copied');
                $this->countProductToReportWishlist($itemId);
            } else {
                $this->messageManager->addNoticeMessage('Product has already existed');
            }
            return $this->_redirect('wishlist',array('wishlistId'=>$wishlistId));
        } elseif ($wishlistId === 'new') {

            /**
             * @var $wishlist \Magenest\MultipleWishlist\Model\MultipleWishlist
             */
            $wishlist = $this->multipleWishlistModelFactory->create();

            $newWishlistName = !empty($requestParams['newName']) ? $requestParams['newName'] : 'default name';

            $customerId = $this->_session->getCustomerId();

            $wishlistId = $wishlist->addNewWishlist($customerId, $newWishlistName);
            $value = $item->copyTo($wishlistId);
            if ($value) {
                $this->messageManager->addSuccessMessage('Copied');
                $this->countProductToReportWishlist($itemId);
            } else {
                $this->messageManager->addNoticeMessage('Product has already existed');
            }
            return $this->_redirect('wishlist',array('wishlistId'=>$wishlistId));
        }
    }

    public function countProductToReportWishlist($itemData)
    {
        $itemIdParsed = explode('-', $itemData);
        if ($itemIdParsed[0] == 'main') {
            $itemData = $itemIdParsed[1];
            $productId = $this->itemMagentoModelFactory->create()->load($itemData)->getProduct_id();
        } else $productId = $this->itemModelFactory->create()->load($itemData)->getProduct_id();
        if (isset($productId)) {
            $check = $this->reportCollectionFactoty->create()->addFieldToFilter('product_id', $productId)->getData();
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
            }
        }
        $customerSession = $this->customerSession;
        if ($customerSession->isLoggedIn()) {
            $wishlistCollection = $this->collectionFactory->create()->addFieldToFilter('customer_id', $customerSession->getCustomer()->getId())->getData();
            $wishlist = $this->wishlistFactory->create()->load($wishlistCollection['0']['wishlist_id']);
            if ($wishlist->getNotification() != null) {
                $notification = $this->serializer->unserialize($wishlist->getNotification());
                if(count($notification)>0){
                    if (!in_array((int)$productId, $notification, true)) {
                        array_push($notification, (int)$productId);
                        $wishlist->setNotification($this->serializer->serialize($notification));
                        $this->resourceWishList->save($wishlist);
                    }
                }
            } else {
                $return = array();
                array_push($return, (int)$productId);
                $wishlist->setNotification($this->serializer->serialize($return));
                $this->resourceWishList->save($wishlist);
            }
        }
    }
}