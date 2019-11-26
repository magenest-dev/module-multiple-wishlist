<?php
/**
 * Created by PhpStorm.
 * User: chung
 * Date: 4/6/17
 * Time: 11:56 PM
 */

namespace Magenest\MultipleWishlist\Controller\Index;

/**
 * Class Update
 * @package Magenest\MultipleWishlist\Controller\Index
 */
class Update extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magenest\MultipleWishlist\Model\ItemFactory
     */
    protected $itemModelFactory;

    /**
     * Update constructor.
     * @param \Magenest\MultipleWishlist\Model\ItemFactory $itemModelFactory
     * @param \Magento\Framework\App\Action\Context $context
     */
    public function __construct(
        \Magenest\MultipleWishlist\Model\ItemFactory $itemModelFactory,
        \Magento\Framework\App\Action\Context $context
    )
    {
        $this->itemModelFactory = $itemModelFactory;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $requestParams = $this->getRequest()->getParams();
        $qtys = isset($requestParams['qty']) ? $requestParams['qty'] : null;

        /** @var \Magenest\MultipleWishlist\Model\Item $itemF */
        if (!is_array($qtys)) {
            $this->messageManager->addErrorMessage('Something went wrong');
            return $this->_redirect('wishlist');
        }

        foreach ($qtys as $key => $qty) {
            $item = $this->itemModelFactory->create()->load($key);
            if ($item->isOwner()) {
                $item->setQty($qty);
            }
        }

        $this->messageManager->addSuccessMessage('Updated to Wishlist');
        return $this->_redirect('wishlist');
    }
}