<?php

namespace Magenest\MultipleWishlist\Plugin;
use Magento\Framework\Controller\ResultFactory;

/**
 * Class WishlistPlugin
 * @package Magenest\MultipleWishlist\Plugin
 */
class setMessageAddToAllCart
{
    protected $messageManager;
    public function __construct(
        \Magento\Framework\Message\ManagerInterface $messageManager
    )
    {
        $this->messageManager = $messageManager;
    }

    public function afterExecute(\Magento\Wishlist\Controller\Index\Allcart $subject, $resulf)
    {
        $this->messageManager->getMessages(true);
        return $resulf;
    }
}