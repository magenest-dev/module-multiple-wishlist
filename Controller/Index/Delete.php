<?php
/**
 * Created by PhpStorm.
 * User: chung
 * Date: 3/10/17
 * Time: 10:09 AM
 */

namespace Magenest\MultipleWishlist\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;


/**
 * Class Delete
 * @package Magenest\MultipleWishlist\Controller\Index
 */
class Delete extends Action
{
    /**
     * @var \Magenest\MultipleWishlist\Model\MultipleWishlistFactory
     */
    protected $multiplewishModelFactory;

    /**
     * Delete constructor.
     * @param Context $context
     * @param \Magenest\MultipleWishlist\Model\MultipleWishlistFactory $multiplewishModelFactory
     */
    public function __construct(
        Context $context,
        \Magenest\MultipleWishlist\Model\MultipleWishlistFactory $multiplewishModelFactory
    )
    {
        $this->multiplewishModelFactory = $multiplewishModelFactory;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     * @throws \Exception
     */
    public function execute()
    {
        $requestParams = $this->getRequest()->getParams();

        $wishlistId = isset($requestParams['wishlist-id']) ? $requestParams['wishlist-id'] : null;

        if (isset($wishlistId)) {
            /**
             * @var $mwl \Magenest\MultipleWishlist\Model\MultipleWishlist
             */
            $mwl = $this->multiplewishModelFactory->create()->load($wishlistId);
            $mwl->delete();
        } else {
            // TODO: show error
        }

        return $this->_redirect('wishlist');
    }
}