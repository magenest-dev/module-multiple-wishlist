<?php

namespace Magenest\MultipleWishlist\Plugin;
use Magento\Framework\Controller\ResultFactory;

/**
 * Class WishlistPlugin
 * @package Magenest\MultipleWishlist\Plugin
 */
class setUrlRemoveInMain
{
    protected $_urlInterface;
    protected $resultFactory;
    protected $itemFactory;
    protected $wishlistFactory;
    protected $serializer;
    protected $helper;
    public function __construct(
        \Magento\Framework\UrlInterface $urlInterface,
        \Magento\Framework\App\Action\Context $context,
        \Magento\Wishlist\Model\ItemFactory $itemFactory,
        \Magento\Wishlist\Model\WishlistFactory $wishlistFactory,
        \Magento\Framework\Serialize\SerializerInterface $serializer,
        \Magenest\MultipleWishlist\Helper\Data $helper
    )
    {
        $this->_urlInterface = $urlInterface;
        $this->resultFactory = $context->getResultFactory();
        $this->itemFactory = $itemFactory;
        $this->wishlistFactory = $wishlistFactory;
        $this->serializer = $serializer;
        $this->helper = $helper;
    }

    public function beforeExecute(\Magento\Wishlist\Controller\Index\Remove $subject)
    {
        $id = (int)$subject->getRequest()->getParam('item');
        $customerId = (int)$subject->getRequest()->getParam('customer_id');
        $item = $this->itemFactory->create()->load($id);
        if($item->getId())
        {
            $wishlist = $this->wishlistFactory->create()->load($customerId, 'customer_id');
            if (!$wishlist->getWislistId()) {
                $notification = $wishlist->getNotification();
                if ($notification != null){
                    $notification = $this->serializer->unserialize($notification);
                    $checkBeforeDelete = $this->helper->checkBeforeDelete(null,$item->getProductId(),$customerId);
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
        }
    }

    public function afterExecute(\Magento\Wishlist\Controller\Index\Remove $subject, $resulf)
    {
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $url = $this->_urlInterface->getUrl('wishlist/index/index');
        $resultRedirect->setUrl($url);
        return $resultRedirect;
    }
}