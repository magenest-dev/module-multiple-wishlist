<?php

namespace Magenest\MultipleWishlist\Model;

//use Magento\Catalog\Api\ProductRepositoryInterface;
//use Magento\Framework\Exception\NoSuchEntityException;
use Magenest\MultipleWishlist\Model\ResourceModel\Item\CollectionFactory;
use Magenest\MultipleWishlist\Model\ResourceModel\MultipleWishlist\Collection as CollectionWishlist;
use Magenest\MultipleWishlist\Model\ResourceModel\MultipleWishlist as ResourceWishlist;
/**
 * Wishlist model
 *
 * @method \Magenest\MultipleWishlist\Model\ResourceModel\MultipleWishlist _getResource()
 * @method \Magenest\MultipleWishlist\Model\ResourceModel\MultipleWishlist getResource()
 * @method int getShared()
 * @method \Magenest\MultipleWishlist\Model\MultipleWishlist setShared(int $value)
 * @method string getSharingCode()
 * @method \Magenest\MultipleWishlist\Model\MultipleWishlist setSharingCode(string $value)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class MultipleWishlist extends \Magento\Framework\Model\AbstractModel
{
    const CACHE_TAG = 'multiplewishlist';

    protected $_eventPrefix = 'multiplewishlist';
    protected $itemFactory;
    protected $_customerSession;
    protected $isLoggedIn;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Store filter for wishlist
     *
     * @var \Magento\Store\Model\Store
     */
    protected $_store;

    /**
     * Shared store ids (website stores)
     *
     * @var array
     */
    protected $_storeIds;

    /**
     * Wishlist item collection
     *
     * @var \Magenest\MultipleWishlist\Model\ResourceModel\Item\Collection
     */
    protected $_itemCollection;


    /**
     * @var ItemFactory
     */
    protected $_mwishlistItemFactory;

    /**
     * @var CollectionFactory
     */
    protected $_mwishlistCollectionFactory;

    /**
     * @var bool
     */
    protected $_useCurrentWebsite;

    protected $radomFactory;

    protected $multipleWishlistResoure;

    protected $multipleWishlistFactory;


    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param ResourceWishlist $resource
     * @param Collection $resourceCollection
     * @param ItemFactory $itemFactory
     * @param CollectionFactory $mwishlistCollectionFactory
     * @param \Magento\Customer\Model\Session $customerSession
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        ResourceWishlist $resource,
        CollectionWishlist $resourceCollection,
        \Magenest\MultipleWishlist\Model\ItemFactory $itemFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        CollectionFactory $mwishlistCollectionFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Math\RandomFactory $radomFactory,
        \Magenest\MultipleWishlist\Model\ResourceModel\MultipleWishlist $multipleWishlistResoure,
        \Magenest\MultipleWishlist\Model\MultipleWishlistFactory $multipleWishlistFactory,
        $useCurrentWebsite = true,
        array $data = array()
    )
    {
        $this->_customerSession = $customerSession;
        $this->isLoggedIn = $this->_customerSession->isLoggedIn();
        $this->itemFactory = $itemFactory;
        $this->_storeManager = $storeManager;
        $this->_mwishlistCollectionFactory = $mwishlistCollectionFactory;
        $this->_init('Magenest\MultipleWishlist\Model\ResourceModel\MultipleWishlist');
        $this->radomFactory = $radomFactory;
        $this->multipleWishlistResoure = $multipleWishlistResoure;
        $this->multipleWishlistFactory = $multipleWishlistFactory;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    public function addNewWishlist($customerId, $wishlistName)
    {
        $data = array(
            'name' => $wishlistName,
            'customer_id' => $customerId,
            'sharing_code' => $this->_getSharingRandomCode()
        );
        /*$this->setData($data);*/
        $this->multipleWishlistResoure->save($this->setData($data));

        return $this->getId();
    }

    public function loadByCustomerId($customerId, $create = false)
    {
        if ($customerId === null) {
            return $this;
        }
        $customerId = (int)$customerId;
        $customerIdFieldName = $this->_getResource()->getCustomerIdFieldName();
        $this->_getResource()->load($this, $customerId, $customerIdFieldName);
        if (!$this->getId() && $create) {
            $this->multipleWishlistResoure->save($this->setCustomerId($customerId),$this->setSharingCode($this->generateSharingCode()));
        }
        return $this;
    }


    /**
     * Set random sharing code
     *
     * @return $this
     */
    public function generateSharingCode()
    {
        $this->setSharingCode($this->_getSharingRandomCode());
        return $this;
    }
    /**
     * Set customer id
     *
     * @param int $customerId
     * @return $this
     */
    public function setCustomerId($customerId)
    {
        return $this->setData($this->_getResource()->getCustomerIdFieldName(), $customerId);
    }


    protected function _getSharingRandomCode()
    {
        $mathRandom = $this->radomFactory->create();
        return $mathRandom->getUniqueHash();
    }

    public function addProduct($productId, $qty = 1, $buyRequest = null)
    {
        if ($productId !== null) {
            $return = array();

            if ($this->isOwner()) {
                $wishlistId = $this->getId();
                $item = $this->itemFactory->create();
                $return = $item->addItem($wishlistId, $productId, $qty, $buyRequest);
            }
            return $return;
        }
        return false;
    }

    public function isOwner()
    {
        $return = false;
        if ($this->_customerSession->isLoggedIn()) {
            $customerId = $this->_customerSession->getCustomerId();
            if ($this->getData('customer_id') === $customerId) {
                $return = true;
            }
        }
        return $return;
    }

    /**
     * Load by sharing code
     *
     * @param string $code
     * @return $this
     */
    public function loadByCode($code)
    {
        $this->_getResource()->load($this, $code, 'sharing_code');
        return $this;
    }

    /**
     * Set wishlist store
     *
     * @param \Magento\Store\Model\Store $store
     * @return $this
     */
    public function setStore($store)
    {
        $this->_store = $store;
        return $this;
    }

    /**
     * Retrieve wishlist store object
     *
     * @return \Magento\Store\Model\Store
     */
    public function getStore()
    {
        if ($this->_store === null) {
            $this->setStore($this->_storeManager->getStore());
        }
        return $this->_store;
    }

    /**
     * Retrieve shared store ids for current website or all stores if $current is false
     *
     * @return array
     */
    public function getSharedStoreIds()
    {
        if ($this->_storeIds === null || !is_array($this->_storeIds)) {
            if ($this->_useCurrentWebsite) {
                $this->_storeIds = $this->getStore()->getWebsite()->getStoreIds();
            } else {
                $_storeIds = [];
                $stores = $this->_storeManager->getStores();
                foreach ($stores as $store) {
                    $_storeIds[] = $store->getId();
                }
                $this->_storeIds = $_storeIds;
            }
        }
        return $this->_storeIds;
    }

    /**
     * Retrieve wishlist item collection
     *
     * @return \Magenest\MultipleWishlist\Model\ResourceModel\Item\Collection
     */
    public function getItemCollection()
    {
        if ($this->_itemCollection === null) {
            $this->_itemCollection = $this->_mwishlistCollectionFactory->create()->addWishlistFilter(
                $this
            )->addStoreFilter(
                $this->getSharedStoreIds()
            )->setVisibilityFilter();
        }

        return $this->_itemCollection;
    }

    public function setName($newName)
    {
        if (!$this->isOwner()) return -1;
        if ($this->isOwner()) {
            $this->multipleWishlistResoure->save($this->setData('name', $newName));
            return 0;
        }
    }

    public function delete()
    {
        if ($this->isOwner()) {
            $item = $this->itemFactory->create();
            /**
             * @var $item \Magenest\MultipleWishlist\Model\Item
             */
            $item->deleteByWishlist($this->getId());
            parent::delete();
        }
    }

    /**
     * @return array
     */
    public function getProducts()
    {
        $return = array();
        if ($this->isOwner()) {
            $wishlistId = $this->getId();
            $item = $this->itemFactory->create();
            $return = $item->getItems($wishlistId);
        }
        return $return;
    }

    public function getProductsSharing()
    {
        $return = array();
            $wishlistId = $this->getId();
            $item = $this->itemFactory->create();
            $return = $item->getItems($wishlistId);
        return $return;
    }
    /**
     * @return array
     *
     */
    public function loadWishlist()
    {
        $return = array();

        if (!$this->isLoggedIn) {
            return $return;
        } else {
            $session = $this->_customerSession;
            $customerId = $session->getCustomerId();

            $wishlistColection = $this->getCollection()->addFieldToFilter('customer_id', $customerId);
            $return = $wishlistColection->getData();

//            foreach ($wishlists as $wishlist) {
//                array_push($return, $wishlist);
//            }
        }

        return $return;
    }

    public function getCountWishlist()
    {
        $return = 0;

        if (!$this->isLoggedIn) {
            return $return;
        } else {
            $session = $this->_customerSession;
            $customerId = $session->getCustomerId();

            $wishlistColection = $this->getCollection()->addFieldToFilter('customer_id', $customerId);
            $return = $wishlistColection->count();

//            foreach ($wishlists as $wishlist) {
//                array_push($return, $wishlist);
//            }
        }

        return $return;
    }

    public function getAllItemsCount()
    {
        $return = 0;

        if (!$this->isLoggedIn) {
            return $return;
        } else {
            $session = $this->_customerSession;
            $customerId = $session->getCustomerId();

            $wishlistColection = $this->getCollection()->addFieldToFilter('customer_id', $customerId);
            $wishlists = $wishlistColection->getItems();

            foreach ($wishlists as $wishlist) {
                $return += count($wishlist->getItemIds());
            }
        }

        return $return;
    }

    public function getItemIds()
    {
        $return = array();

        if ($this->isOwner()) {
            $itemF = $this->itemFactory->create();
            $itemCollection = $itemF->getCollection()->addFieldToFilter('wishlist_id', $this->getId());
            $return = $itemCollection->getAllIds();
        }

        return $return;
    }

}