<?php

namespace Magenest\MultipleWishlist\Controller\Index;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\State;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\MailException;
use Magento\Framework\Exception\NoSuchEntityException;

class Sendemail extends \Magento\Framework\App\Action\Action
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

    protected $_collectionWishListFactory;

    protected $_collectionWishListItemFactory;

    protected $_collectionWishListCoreFactory;

    protected $_collectionWishListCoreItemFactory;

    protected $helper;

    protected $wishlistFactory;

    protected $resourceWishList;

    protected $cron;

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
        \Magenest\MultipleWishlist\Cron\SendMailProductOutOfStock $cron
    )
    {
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
        $this->cron = $cron;
        parent::__construct($context);
    }

    public function execute()
    {
        /*$templateLayout = 'multiplewishlist_test_template';
        $customerData = $this->helper->getAllCustomer();
        foreach ($customerData as $value)
        {
            $customerId = $value['entity_id'] ;
            $collectionWishList = $this->helper->getProductInMain($customerId);
            $templateVars = array(
                'store' => $this->_storeManager->getStore(),
                'customer' => $value['entity_id'],
                'name' => $value['firstname'],
                'gmail' => $value['email'],
                'collectionWishList' =>$collectionWishList,
            );
            if($value['email'] && $collectionWishList->count()>0){
                $this->helper->sendMail($value['email'], $templateVars, $templateLayout);
            }
        }*/
        /** @var \Magento\Store\Model\App\Emulation $emulation */

//
//		$state->emulateAreaCode(
//			"crontab",
//			function(){
				$this->cron->execute();
//			}
//		);

	}
}