<?php
/**
 * Created by PhpStorm.
 * User: chung
 * Date: 4/2/17
 * Time: 8:54 PM
 */

namespace Magenest\MultipleWishlist\Controller\Item;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\ObjectManager;

class Description extends Action
{
    protected $itemModelFactory;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magenest\MultipleWishlist\Model\ItemFactory $itemModelFactory)
    {
        $this->itemModelFactory = $itemModelFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $requestParams = $this->getRequest()->getParams();

        $itemId = isset($requestParams['item-id']) ? $requestParams['item-id'] : null;
        $description= isset($requestParams['description']) ? $requestParams['description'] : null;

        if (isset($itemId )) {
            $itemFactory = $this->itemModelFactory->create();
            /**
             * @var $item \Magenest\MultipleWishlist\Model\Item
             */
            $item = $itemFactory->load($itemId);
            $item->setDescription($description);
            $this->messageManager->addSuccessMessage('Updated');
        } else {
            $this->messageManager->addErrorMessage('No item id');
            // TODO: thong bao' loi~
        }

        return $this->_redirect('wishlist');
    }
}