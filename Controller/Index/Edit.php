<?php
/**
 * Created by PhpStorm.
 * User: chung
 * Date: 3/10/17
 * Time: 8:29 AM
 */

namespace Magenest\MultipleWishlist\Controller\Index;


/**
 * Class Edit
 * @package Magenest\MultipleWishlist\Controller\Index
 */
class Edit extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magenest\MultipleWishlist\Model\MultipleWishlistFactory
     */
    protected $multipleWishlistFactory;

    /**
     * Edit constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magenest\MultipleWishlist\Model\MultipleWishlistFactory $multipleWishlistFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magenest\MultipleWishlist\Model\MultipleWishlistFactory $multipleWishlistFactory
    )
    {
        $this->multipleWishlistFactory = $multipleWishlistFactory;
        parent::__construct($context);
    }


    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $requestParams = $this->getRequest()->getParams();

        $wishlistId = isset($requestParams['wishlist-id']) ? $requestParams['wishlist-id'] : null;
        $newName = isset($requestParams['new-name']) ? $requestParams['new-name'] : null;

        if (isset($wishlistId)) {
            $mwlFactory = $this->multipleWishlistFactory->create();
            $mwl = $mwlFactory->load($wishlistId);
            $mwl->setName($newName);
        } else {
            //
        }

        return $this->_redirect('wishlist',array('wishlistId'=>$wishlistId));
    }
}