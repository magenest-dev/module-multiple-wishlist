<?php

namespace Magenest\MultipleWishlist\Plugin;
use Magento\Framework\Controller\ResultFactory;

/**
 * Class WishlistPlugin
 * @package Magenest\MultipleWishlist\Plugin
 */
class setMessageAddToCart
{
    protected $messageManager;
    public function __construct(
        \Magento\Framework\Message\ManagerInterface $messageManager
    )
    {
        $this->messageManager = $messageManager;
    }

    public function afterExecute(\Magento\Wishlist\Controller\Index\Cart $subject, $resulf)
    {
        if($this->messageManager->getMessages()->getCountByType('notice')>0)
        {
            return $resulf;
        }else{
            $this->messageManager->getMessages(true);
            return $resulf;
        }
    }
}