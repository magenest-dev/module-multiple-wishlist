<?php
/**
 * Created by PhpStorm.
 * User: chung
 * Date: 3/29/17
 * Time: 10:15 AM
 */

namespace Magenest\MultipleWishlist\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;


/**
 * Class Cart
 * @package Magenest\MultipleWishlist\Controller\Index
 */
class Cart extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magenest\MultipleWishlist\Model\ItemFactory
     */
    protected $itemModelFactory;
    /**
     * @var \Magento\Checkout\Model\Cart
     */
    protected $cartModelFactory;

    protected $productCollectionFactory;

    protected $productFactory;
    /**
     * Cart constructor.
     * @param Context $context
     * @param \Magenest\MultipleWishlist\Model\ItemFactory $itemModelFactory
     * @param \Magento\Checkout\Model\Cart $cartModelFactory
     */
    public function __construct(
        Context $context,
        \Magenest\MultipleWishlist\Model\ItemFactory $itemModelFactory,
        \Magento\Checkout\Model\Cart $cartModelFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory
    )
    {
        $this->itemModelFactory = $itemModelFactory;
        $this->cartModelFactory = $cartModelFactory;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->productFactory = $productFactory;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $requestParams = $this->getRequest()->getParams();
        $itemId = isset($requestParams['item']) ? $requestParams['item'] : null;
        $qty = isset($requestParams['qty'][$itemId]) ? $requestParams['qty'][$itemId] : null;
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($itemId == null) return $resultRedirect;;

        /** @var \Magenest\MultipleWishlist\Model\Item $itemF */
        $item = $this->itemModelFactory->create()->load($itemId);

        $result = $item->addToCart($qty);
        $this->cartModelFactory->save();
        if (is_string($result)) {
/*            $this->messageManager->addNoticeMessage(__('You need to choose options for your item before adding to cart.'));*/
            $resultRedirect = $this->resultRedirectFactory->create();
            $redirectUrl = $this->_url->getUrl('*/*');
            $productId = $this->itemModelFactory->create()->load($itemId)->getProductId();
            $productModel = $this->productFactory->create()->load($productId);
            $productUrl = $productModel->getProductUrl();
            $redirectUrl = $productUrl;
            $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
            $resultJson->setData(['backUrl' => $redirectUrl]);
            $productType = $productModel->getTypeId();
            if($productType == 'downloadable')
            {
                $this->messageManager->addNoticeMessage('Please specify product link(s).');
            }elseif ($productType == 'configurable'){
                $this->messageManager->addNoticeMessage('You need to choose options for your item.');
            }
            return $resultJson;
        } else {
/*            $this->messageManager->addSuccessMessage('Added to Cart');*/
            $redirectUrl = $this->_url->getUrl('*/*');
            $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
            $resultJson->setData(['backUrl' => $redirectUrl]);
            return $resultJson;
        }
    }
}