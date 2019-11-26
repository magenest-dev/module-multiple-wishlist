<?php

namespace Magenest\MultipleWishlist\Plugin;

/**
 * Class WishlistPlugin
 * @package Magenest\MultipleWishlist\Plugin
 */
class WishlistPlugin
{
    /**
     * @var \Magenest\MultipleWishlist\Model\MultipleWishlistFactory
     */
    protected $multipleWishlistFactory;

//    protected $_resultJsonFactory;

    /**
     * WishlistPlugin constructor.
     * @param \Magenest\MultipleWishlist\Model\MultipleWishlistFactory $multipleWishlistFactory
     */
    public function __construct(
//        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magenest\MultipleWishlist\Model\MultipleWishlistFactory $multipleWishlistFactory
    )
    {
        $this->multipleWishlistFactory = $multipleWishlistFactory;
    }

    /**
     * @param \Magento\Wishlist\Controller\Index\Index $subject
     * @param callable $proceed
     * @return mixed
     */
    public function aroundExecute(\Magento\Wishlist\Controller\Index\Index $subject, callable $proceed)
    {
        $mwishlistId = $subject->getRequest()->getParam('wishlist_id');
        if (empty($mwishlistId)) {
            return $proceed();
        } else {
            $wishlist = $this->multipleWishlistFactory->create();

            $wishlist = $wishlist->load((int)$mwishlistId);

            $return = $wishlist->getProducts();

            var_dump($return);
        }
    }
}