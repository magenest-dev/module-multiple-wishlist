<?php
/**
 * Created by PhpStorm.
 * User: chung
 * Date: 3/11/17
 * Time: 10:09 AM
 */

namespace Magenest\MultipleWishlist\Controller\Item;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Framework\Session\SessionManagerInterface;

class Delete extends Action
{
    protected $itemModelFactory;
    protected $itemMagentoModelFactory;
    protected $_cookieManager;
    protected $_cookieMetadataFactory;
    protected $_sessionManager;
    protected $resultFactory;
    const wishlishId = 'wishlishId';
    const COOKIE_DURATION = 2; // 5p
	protected $serializer;
	protected $wishlistFactory;
	protected $customerSession;
    protected $multipleWishlistCollection;
    protected $multipleItemCollection;
    protected $wishlistCollection;
    protected $wishlistItemCollection;
    protected $helper;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magenest\MultipleWishlist\Model\ItemFactory $itemModelFactory,
        CookieManagerInterface $cookieManager,
        CookieMetadataFactory $cookieMetadataFactory,
        SessionManagerInterface $sessionManager,
        \Magento\Framework\Controller\ResultFactory $resulFactory,
		\Magento\Framework\Serialize\SerializerInterface $serializer,
		\Magento\Wishlist\Model\WishlistFactory $wishlistFactory,
		\Magento\Customer\Model\Session $customerSession,
        \Magenest\MultipleWishlist\Model\ResourceModel\MultipleWishlist\CollectionFactory $multipleWishlistCollection,
        \Magenest\MultipleWishlist\Helper\Data $helper
)
    {
        $this->itemModelFactory = $itemModelFactory;
        $this->_cookieManager = $cookieManager;
        $this->_cookieMetadataFactory = $cookieMetadataFactory;
        $this->_sessionManager = $sessionManager;
        $this->resultFactory = $resulFactory;
        $this->serializer = $serializer;
        $this->wishlistFactory = $wishlistFactory;
		$this->customerSession =$customerSession;
		$this->multipleWishlistCollection = $multipleWishlistCollection;
		$this->helper = $helper;
        parent::__construct($context);
    }

    public function execute()
    {
        $requestParams = $this->getRequest()->getParams();

        $wishlistId = isset($requestParams['wishlistId']) ? $requestParams['wishlistId'] : null;
        $itemId = isset($requestParams['item-id']) ? $requestParams['item-id'] : null;
        if (isset($itemId)) {
            $itemFactory = $this->itemModelFactory->create();
            /**
             * @var $item \Magenest\MultipleWishlist\Model\Item
             */
            $item = $itemFactory->load($itemId );
			$wishlist = $this->wishlistFactory->create()->load($this->customerSession->getCustomerId(), 'customer_id') ;
			if (!$wishlist->getWislistId()) {
				$notification = $wishlist->getNotification();
				if ($notification != null){
					$notification = $this->serializer->unserialize($notification);
					$checkBeforeDelete = $this->helper->checkBeforeDelete($wishlistId,$item->getProductId(),$this->customerSession->getCustomerId());
					if(count($notification)>0){
                        if(in_array($item->getProductId(),$notification) && $checkBeforeDelete != false){
                            $key = array_search($item->getProductId(), $notification);
                            unset($notification[$key]);
                            $wishlist->setNotification($this->serializer->serialize($notification));
                            $wishlist->save();
                        }
                    }
				}
			}
            $item->delete();
            $this->messageManager->addSuccessMessage('Deleted');
        } else {
            $this->messageManager->addErrorMessage('No item id');
            // TODO: thong bao' loi~
        }
        return $this->_redirect('wishlist',array('wishlistId'=>$wishlistId));
    }

}