<?php
/**
 * Created by PhpStorm.
 * User: chung
 * Date: 3/24/17
 * Time: 10:37 AM
 */

namespace Magenest\MultipleWishlist\Controller\Index;


/**
 * Class Allcart
 * @package Magenest\MultipleWishlist\Controller\Index
 */
class Allcart extends \Magento\Framework\App\Action\Action
{
    /**
     * @var
     */
    protected $_pageFactory;
    /**
     * @var \Magenest\MultipleWishlist\Model\ItemFactory
     */
    protected $_itemFactory;
    /**
     * @var \Magento\Checkout\Model\Cart
     */
    protected $_cart;

    /**
     * Allcart constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magenest\MultipleWishlist\Model\ItemFactory $itemFactory
     * @param \Magento\Checkout\Model\Cart $cart
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magenest\MultipleWishlist\Model\ItemFactory $itemFactory,
        \Magento\Checkout\Model\Cart $cart)
    {
        $this->_cart = $cart;
        $this->_itemFactory = $itemFactory;
        return parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $requestParams = $this->getRequest()->getParams();

        $wishlistId = isset($requestParams['wishlist']) ? $requestParams['wishlist'] : null;

        $itemFactory = $this->_itemFactory->create();
        $return = $itemFactory->getItems($wishlistId);
        foreach ($return as $itemId) {
            $item = $this->_itemFactory->create()->load($itemId['id']);
            $item->addToCart((int)$itemId['qty']);
        }
        $this->_cart->save();
    /*    $this->messageManager->addSuccessMessage('Added to Cart');
        $this->messageManager->getMessages(true);*/
        return $this->_redirect($this->_redirect->getRefererUrl());
    }
}