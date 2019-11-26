<?php
/**
 * Created by PhpStorm.
 * User: chung
 * Date: 3/11/17
 * Time: 11:55 AM
 */

namespace Magenest\MultipleWishlist\Controller\Item;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Framework\Session\SessionManagerInterface;

class Move extends Action
{
    protected $_resultJsonFactory;
    protected $_session;
    protected $itemModelFactory;
    protected $multipleWishlistModelFactory;
    protected $itemFactory;
    protected $serializerInterface;

    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        Session $session,
        \Magenest\MultipleWishlist\Model\ItemFactory $itemModelFactory,
        \Magenest\MultipleWishlist\Model\MultipleWishlistFactory $multipleWishlistModelFactory,
        \Magento\Wishlist\Model\ItemFactory $itemFactory,
        \Magento\Framework\Serialize\SerializerInterface $serializerInterface
    )
    {
        $this->_resultJsonFactory = $jsonFactory;
        $this->_session = $session;
        $this->itemModelFactory = $itemModelFactory;
        $this->multipleWishlistModelFactory = $multipleWishlistModelFactory;
        $this->itemFactory = $itemFactory;
        $this->serializerInterface = $serializerInterface;
        parent::__construct($context);
    }

    public function execute()
    {
        $return = $this->_resultJsonFactory->create()->setData(['success' => false]);

        if (!$this->_session->isLoggedIn()) {
            $return->setData(['detail' => 'notLogin']);
            $this->messageManager->addErrorMessage('You are not logged in');
            return $return;
        }

        $itemFactory = $this->itemModelFactory->create();

        $requestParams = $this->getRequest()->getParams();

        $wishlistId = isset($requestParams['wishlist']) ? $requestParams['wishlist'] : null;
        $wishlistIdMove = 0;
        $itemId = isset($requestParams['itemId']) ? $requestParams['itemId'] : null;
//        $newName = isset($requestParams['newName']) ? $requestParams['newName'] : null;

        if (isset($itemId)) {
            $itemIdParsed = explode('-', $itemId);
            if ($itemIdParsed[0] == 'main') {
//                if ($wishlistId == 'main') return $return;
                if ($wishlistId == 'new') {
                    /**
                     * @var $wishlist \Magenest\MultipleWishlist\Model\MultipleWishlist
                     */
                    $wishlist = $this->multipleWishlistModelFactory->create();

                    $newWishlistName = !empty($requestParams['newName']) ? $requestParams['newName'] : 'default name';

                    $customerId = $this->_session->getCustomerId();

                    $wishlistId = $wishlist->addNewWishlist($customerId, $newWishlistName);
                }
                // Move from main
                $itemId = $itemIdParsed[1];
                /** @var \Magento\Wishlist\Model\Item $item */
                $item = $this->itemFactory->create()->load($itemId);
                /*$item = $item->loadWithOptions($itemId);*/

                $productId = $item->getProductId();
                $qty = $item->getData('qty');

                $buyRequestArr = [];
                $buyRequestArr['product'] = $productId;

                $options = $item->getOptionsByCode();
                $superAttribute = [];
                /** @var \Magento\Wishlist\Model\Item\Option $option */
                foreach ($options as $option) {
                    $optionData = $option->getData();
                    if ($optionData['code'] == 'attributes') {
                        $superAttribute = $this->serializerInterface->unserialize($optionData['value']);
                    }
                }
                $buyRequestArr['super_attribute'] = $superAttribute;
                if ($wishlistId === 'main' && $item->getDataByKey('wishlist_id') == 1) {
                    $this->messageManager->addNoticeMessage('Product has already existed');
                    return $this->_redirect('wishlist',array('wishlistId'=>0));
                }
                $value = $itemFactory->addItem($wishlistId, $productId, $qty, $buyRequestArr);
                if ($value) {
                    $item->delete();
                    $this->messageManager->addSuccessMessage('Moved');
                } else {
                    $this->messageManager->addNoticeMessage('Product has already existed');
                }
                return $this->_redirect('wishlist',array('wishlistId'=>$wishlistId));
            } else {
                /**
                 * @var $item \Magenest\MultipleWishlist\Model\Item
                 */
                $item = $itemFactory->load($itemId);
            }
        } else {
            $return->setData(['detail' => 'no item']);
            return $return;
        }

        if ($wishlistId === 'main') {

            $checkQty = $item->moveToMain();
            if ($checkQty) {
                $this->messageManager->addSuccessMessage('Moved');
            } else {
                $this->messageManager->addNoticeMessage('Product has already existed');
            }
            return $this->_redirect('wishlist',array('wishlistId'=>0));
        } elseif (is_numeric($wishlistId)) {
            $wishlistId = $wishlistId + 0;

            if (!is_int($wishlistId)) {
                /**
                 * @var $wishlist \Magenest\MultipleWishlist\Model\MultipleWishlist
                 */
                $mwlFactory = $this->multipleWishlistModelFactory->create();
                $wishlist = $mwlFactory->load($wishlistId);

                if (!$wishlist->isOwner()) {
                    $return->setData(['detail' => 'Can\'t specify wishlist.']);
                    return $this->_redirect('wishlist',array('wishlistId'=>0));
                }
            }
            $curItemData = $item->getData();
            if ($curItemData['wishlist_id'] == $wishlistId) {
                $this->messageManager->addNoticeMessage('Product has already existed');
                return $this->_redirect('wishlist',array('wishlistId'=>$wishlistId));
            }
            $value = $item->moveTo($wishlistId);
            if ($value) {
                $this->messageManager->addSuccessMessage('Moved');
            } else {
                $this->messageManager->addNoticeMessage('Product has already existed');
            }
            return $this->_redirect('wishlist',array('wishlistId'=>$wishlistId));
        } elseif ($wishlistId === 'new') {

            /**
             * @var $wishlist \Magenest\MultipleWishlist\Model\MultipleWishlist
             */
            $wishlist = $this->multipleWishlistModelFactory->create();

            $newWishlistName = !empty($requestParams['newName']) ? $requestParams['newName'] : 'default name';

            $customerId = $this->_session->getCustomerId();

            $wishlistId = $wishlist->addNewWishlist($customerId, $newWishlistName);
            $value = $item->moveTo($wishlistId);
            if ($value) {
                $this->messageManager->addSuccessMessage('Moved');
            } else {
                $this->messageManager->addNoticeMessage('Product has already existed');
            }
            return $this->_redirect('wishlist',array('wishlistId'=>$wishlistId));
        }
        $this->messageManager->addErrorMessage('Error');
        return $this->_redirect('wishlist',array('wishlistId'=>0));
    }
}