<?php

namespace Magenest\MultipleWishlist\Cron;

use Magento\Customer\Model\ResourceModel\CustomerRepository;
use Magento\Framework\App\ObjectManager;
use Magento\Store\Model\App\Emulation;

/**
 * Class SendMailProductOutOfStock
 *
 * @package Magenest\MultipleWishlist\Cron
 */
class SendMailProductOutOfStock
{
    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $_request;
    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $_transportBuilder;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magenest\MultipleWishlist\Model\ResourceModel\MultipleWishlist\CollectionFactory
     */
    protected $_collectionWishListFactory;

    /**
     * @var \Magenest\MultipleWishlist\Model\ResourceModel\Item\CollectionFactory
     */
    protected $_collectionWishListItemFactory;

    /**
     * @var \Magento\Wishlist\Model\ResourceModel\Wishlist\CollectionFactory
     */
    protected $_collectionWishListCoreFactory;

    /**
     * @var \Magento\Wishlist\Model\ResourceModel\Item\CollectionFactory
     */
    protected $_collectionWishListCoreItemFactory;

    /**
     * @var \Magenest\MultipleWishlist\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Wishlist\Model\WishlistFactory
     */
    protected $wishlistFactory;

    /**
     * @var \Magento\Wishlist\Model\ResourceModel\Wishlist
     */
    protected $resourceWishList;

	/**
	 * @var \Magento\Framework\App\Config\ScopeConfigInterface
	 */
	protected $scopeConfig;

	/**
	 * @var CustomerRepository
	 */
	protected $customerRepo;

	/**
	 * @var Emulation
	 */
	protected $emulation;

	/**
	 * SendMailProductOutOfStock constructor.
	 *
	 * @param \Magento\Framework\App\Action\Context $context
	 * @param \Magento\Framework\App\Request\Http $request
	 * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
	 * @param \Magento\Store\Model\StoreManagerInterface $storeManager
	 * @param \Magento\Wishlist\Model\ResourceModel\Wishlist\CollectionFactory $collectionWishListCoreFactory
	 * @param \Magento\Wishlist\Model\ResourceModel\Item\CollectionFactory $collectionWishListCoreItemFactory
	 * @param \Magenest\MultipleWishlist\Model\ResourceModel\MultipleWishlist\CollectionFactory $collectionWishListFactory
	 * @param \Magenest\MultipleWishlist\Model\ResourceModel\Item\CollectionFactory $collectionWishListItemFactory
	 * @param \Magenest\MultipleWishlist\Helper\Data $helper
	 * @param \Magento\Wishlist\Model\WishlistFactory $wishlistFactory
	 * @param \Magento\Wishlist\Model\ResourceModel\Wishlist $resourceWishList
	 * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
	 * @param CustomerRepository $customerRepository
	 * @param Emulation $emulation
	 */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Wishlist\Model\ResourceModel\Wishlist\CollectionFactory $collectionWishListCoreFactory,
        \Magento\Wishlist\Model\ResourceModel\Item\CollectionFactory $collectionWishListCoreItemFactory,
        \Magenest\MultipleWishlist\Model\ResourceModel\MultipleWishlist\CollectionFactory $collectionWishListFactory,
        \Magenest\MultipleWishlist\Model\ResourceModel\Item\CollectionFactory $collectionWishListItemFactory,
        \Magenest\MultipleWishlist\Helper\Data $helper,
        \Magento\Wishlist\Model\WishlistFactory $wishlistFactory,
        \Magento\Wishlist\Model\ResourceModel\Wishlist $resourceWishList,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
		CustomerRepository $customerRepository,
		Emulation $emulation
    )
    {
    	$this->customerRepo = $customerRepository;
    	$this->emulation = $emulation;
        $this->_request = $request;
        $this->_transportBuilder = $transportBuilder;
        $this->_storeManager = $storeManager;
        $this->_collectionWishListCoreFactory = $collectionWishListCoreFactory;
        $this->_collectionWishListCoreItemFactory = $collectionWishListCoreItemFactory;
        $this->_collectionWishListFactory = $collectionWishListFactory;
        $this->_collectionWishListItemFactory = $collectionWishListItemFactory;
        $this->helper = $helper;
        $this->wishlistFactory = $wishlistFactory;
        $this->resourceWishList = $resourceWishList;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\MailException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute()
    {
        $enableModule = $this->scopeConfig->getValue('multiplewishlist/general/active', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if ($enableModule == 1) {
            $templateLayout = 'multiplewishlist_cron_send_mail_product_out_of_stock';
            $customerData = $this->helper->getAllCustomer();
			foreach ($customerData as $value) {
                $customerId = $value['entity_id'];
				$customer = $this->customerRepo->getById($customerId);

				$this->emulation->startEnvironmentEmulation($customer->getStoreId(), \Magento\Framework\App\Area::AREA_FRONTEND, true);
				$collectionWishList = $this->helper->getNotification($customerId);
                $templateVars = array(
                    'store' => $this->_storeManager->getStore(),
                    'customer' => $value['entity_id'],
                    'name' => $value['firstname'],
                    'gmail' => $value['email'],
                    'collectionWishList'=> $collectionWishList,
                );
                if(!is_array($collectionWishList))
                {
                    if ($value['email'] && $collectionWishList->count() > 0) {
                        $this->helper->sendMail($value['email'], $templateVars, $templateLayout);
                    }
                }
				$this->emulation->stopEnvironmentEmulation();
			}
        }
    }
}