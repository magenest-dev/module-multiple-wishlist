<?php
/**
 * Created by PhpStorm.
 * User: heomep
 * Date: 04/04/2017
 * Time: 14:27
 */

namespace Magenest\MultipleWishlist\Plugin\Shared;

use Magento\Framework\App\Action\Action;
use Magento\Framework\Controller\ResultFactory;

class Index
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $registry = null;

    /**
     * @var \Magenest\MultipleWishlist\Controller\MultipleWishlistProviderInterface
     */
    protected $multipleWishlistProvider;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Framework\Controller\ResultFactory
     */
    protected $resultFactory;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    protected $_muptiplewishlist;

    protected $wishlistFactory;

    protected $_urlInterface;

    public function __construct(
        \Magenest\MultipleWishlist\Controller\MultipleWishlistProviderInterface $multipleWishlistProvider,
        \Magento\Framework\Registry $registry,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magenest\MultipleWishlist\Model\MultipleWishlistFactory $multipleWishlistFactory,
        \Magento\Wishlist\Model\WishlistFactory $wishlistFactory,
        \Magento\Framework\UrlInterface $urlInterface,
        \Magento\Framework\App\Action\Context $context
    ) {
        $this->multiplewishlistProvider = $multipleWishlistProvider;
        $this->registry = $registry;
        $this->customerSession = $customerSession;
        $this->resultPageFactory = $resultPageFactory;
        $this->_muptiplewishlist = $multipleWishlistFactory;
        $this->wishlistFactory = $wishlistFactory;
        $this->_urlInterface = $urlInterface;
        $this->resultFactory = $context->getResultFactory();
    }

    /**
     * Shared wishlist view page
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function aroundExecute(\Magento\Wishlist\Controller\Shared\Index $subject, callable $proceed)
    {
        $sharingCode = $subject->getRequest()->getParam('code');
        /** @var \Magenest\MultipleWishlist\Model\MultipleWishlist $wishlists */
        $wishlists = $this->_muptiplewishlist->create();
        $wishlist = $wishlists->getCollection()->addFieldToFilter('sharing_code', $sharingCode)->getFirstItem();
        $wishlistId = $wishlist->getData('id');
        if(!empty($wishlistId))
        {
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $url = $this->_urlInterface->getUrl('multiplewishlist/index/sharing',['wishlist'=>$wishlistId]);
            $resultRedirect->setUrl($url);
            return $resultRedirect;
        }else{
            $wishlists = $this->wishlistFactory->create();
            $wishlist = $wishlists->getCollection()->addFieldToFilter('sharing_code', $sharingCode)->getFirstItem();
            $wishlistId = $wishlist->getData('wishlist_id');
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $url = $this->_urlInterface->getUrl('multiplewishlist/index/sharing',['wishlist'=>'main','wishListMainId'=>$wishlistId]);
            $resultRedirect->setUrl($url);
            return $resultRedirect;
        }
       /* if (empty($wishlistId)){
            return $proceed();
        } else{
            $customerId = $this->customerSession->getCustomerId();
            if(!empty($customerId))
            {
                return $proceed();
            } else {
                $resultPage = $this->resultPageFactory->create();
                $resultPage->getLayout()->getBlock('Magenest\MultipleWishlist\Block\Share\MultipleWishlist');
                $resultPage->getLayout()->getBlock('multiplewishlist')->setWishlistId($wishlistId)->toHtml();
                return $resultPage;
            }
        }*/
    }
}
