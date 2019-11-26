<?php

namespace Magenest\MultipleWishlist\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\App\Action;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Validator\Exception;

/**
 * Class Add
 * @package Magenest\MultipleWishlist\Controller\Index
 */
class Add extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $_resultJsonFactory;
    /**
     * @var \Magento\Wishlist\Controller\WishlistProvider
     */
    protected $wishlistProvider;
    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;
    /**
     * @var \Magenest\MultipleWishlist\Model\MultipleWishlistFactory
     */
    protected $multipleWishlistFactory;
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;
    /**
     * @var \Magenest\MultipleWishlist\Controller\MultipleWishlistProviderInterface
     */
    protected $_multipleWishlistProvider;

    protected $configurableproduct;

    protected $_urlInterface;

    protected $itemWishListFactory;

    /**
     * Add constructor.
     * @param Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Wishlist\Controller\WishlistProvider $wishlistProvider
     * @param \Magenest\MultipleWishlist\Controller\MultipleWishlistProviderInterface $multipleWishlistProvider
     * @param ProductRepositoryInterface $productRepository
     * @param \Magenest\MultipleWishlist\Model\MultipleWishlistFactory $multipleWishlistFactory
     */
    public function __construct(
        Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Wishlist\Controller\WishlistProvider $wishlistProvider,
        \Magenest\MultipleWishlist\Controller\MultipleWishlistProviderInterface $multipleWishlistProvider,
        ProductRepositoryInterface $productRepository,
        \Magenest\MultipleWishlist\Model\MultipleWishlistFactory $multipleWishlistFactory,
        \Magento\ConfigurableProduct\Model\Product\Type\Configurable $configurableproduct,
        \Magento\Framework\UrlInterface $urlInterface,
        \Magento\Wishlist\Model\ItemFactory $itemWishListFactory
    )
    {
        $this->multipleWishlistFactory = $multipleWishlistFactory;
        $this->_resultJsonFactory = $resultJsonFactory;
        $this->wishlistProvider = $wishlistProvider;
        $this->_multipleWishlistProvider = $multipleWishlistProvider;
        $this->productRepository = $productRepository;
        $this->_customerSession = $customerSession;
        $this->configurableproduct = $configurableproduct;
        $this->_urlInterface = $urlInterface;
        $this->itemWishListFactory = $itemWishListFactory;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Json|\Magento\Framework\Controller\ResultInterface
     * @throws NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        $return = $this->_resultJsonFactory->create()->setData(['success' => false]);
        $noti = false;
        $requestParams = $this->getRequest()->getParams();
        $isAjax = isset($requestParams['ajax']) ? true : false;

        $requestData = $this->getRequest()->getParam('data');
        $super_attribute = array();
        $wishlistIdSend=0;
        if (!empty($requestData)) {
            $strAtribute = 'super_attribute[';
            foreach ($requestData as $key => $value) {
                $isAttribute = strpos($key, $strAtribute);
                if ($isAttribute !== false) {
                    $keyData = substr($key, strlen($strAtribute));
                    $super_attribute[$keyData] = $value;
                    unset($requestData[$key]);
                }
            }
        }
        $requestData['super_attribute'] = $super_attribute;
        // $requestData === $requestParams without $formkey

        $qty = isset($requestData['qty']) ? $requestData['qty'] : 1;


        if (!$isAjax) {
            $this->_redirect('wishlist');
        }

        $session = $this->_customerSession;

        if (!$session->isLoggedIn()) {
            $return->setData(['detail' => 'notLogin']);
            $this->messageManager->addErrorMessage('Not Login');
            if ($isAjax) return $return;
        } else {

            $wishlistName = isset($requestParams['wishlist']) ? $requestParams['wishlist'] : null;
            $productId = isset($requestParams['productId']) ? (int)$requestParams['productId'] : null;

            if ($productId) {
                $product = $this->productRepository->getById($productId);
                if (!$product || !$product->isVisibleInCatalog()) {
                    $productId = null;
                }
            }
            if (array_count_values($requestData['super_attribute'])!=null) {
                if ($product->getTypeId() == \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
                $attributes = $requestData['super_attribute'];
                $new_product = $this->configurableproduct->getProductByAttributes($attributes, $product);
                $newProductId = $new_product->getID();
                $productId= $newProductId;
                $product = $new_product;
            }
            }

            if ($wishlistName === 'main') {

                $buyRequest = new \Magento\Framework\DataObject($requestData);

                $wishlist = $this->wishlistProvider->getWishlist();

                $wishlistResult = $wishlist->addNewItem($product, $buyRequest);

                if (is_string($wishlistResult)) {
                    $return->setData(['detail' => $wishlistResult]);
                    $this->messageManager->addErrorMessage($wishlistResult);
                    if ($isAjax) return $return;
                }
                $modelItemFactory = $this->itemWishListFactory->create()->load($wishlistResult->getData('wishlist_item_id'));
                $qty = $modelItemFactory->getData('qty');
                if($qty == 1){
                    $wishlist->save();
                }elseif ($qty > 1){
                    $noti = true;
                    $modelItemFactory->setQty(1);
                    $modelItemFactory->save();
                    $wishlist->save();
                }
                $return->setData(['success' => true]);

            } else {
                if ($wishlistName === 'new') {

                    $wishlist = $this->multipleWishlistFactory->create();

                    $newWishlistName = !empty($requestParams['newName']) ? $requestParams['newName'] : 'Default Name';

                    $customerId = $session->getCustomerId();

                    $wishlistId = $wishlist->addNewWishlist($customerId, $newWishlistName);
                    if($productId==null)
                    {
                        $url = $this->_urlInterface->getUrl('wishlist/index/index', ['addNewId' => $wishlistId]);
                        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
                        $resultRedirect->setUrl($url);
                        return $resultRedirect;
                    }
                    $wishlistIdSend=$wishlistId;
                    $wishlist->addProduct($productId, $qty, $requestData);
                    $return->setData(['success' => true, 'id' => $wishlistId, 'name' => $newWishlistName]);

                } else if (is_numeric($wishlistName)) {

                    $wishlistId = $wishlistName + 0;
                    if (!is_int($wishlistId)) {
                        $return->setData(['detail' => 'Can\'t specify wishlist.']);
                        $this->messageManager->addErrorMessage('Can\'t specify wishlist');
                        if ($isAjax) return $return;
                    }

                    $wishlist = $this->multipleWishlistFactory->create();
                    $wishlist = $wishlist->load($wishlistId);
                    $wishlistIdSend=$wishlistId;
                    $check=$wishlist->addProduct($productId, $qty, $requestData);
                    if($check==false){
                        $noti = true;
                    }
                    $return->setData(['success' => true]);
                }
            }
        }
        $url = $this->_urlInterface->getUrl('wishlist/index/index', ['addNewId' => $wishlistIdSend]);
        if($noti)
        {
            $message = __('Product has already existed in wish list. ');
            $message .= '<a href="'.$url.'">'.__('Click to view.').'</a>';
            $this->messageManager->addNotice($message);
        }else{
            $message = __('Add to wishlist successfully. ');
            $message .= '<a href="'.$url.'">'.__('Click to view.').'</a>';
            $this->messageManager->addSuccess($message);
        }
        $this->_eventManager->dispatch('Add_Product_to_ReportWishlist', ['model' => $productId]);
        return $return;
    }
}
