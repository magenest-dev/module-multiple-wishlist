<?php
/**
 * Created by PhpStorm.
 * User: heomep
 * Date: 30/03/2017
 * Time: 09:49
 */

namespace Magenest\MultipleWishlist\Plugin;

use Magento\Framework\App\Action;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Session\Generic as WishlistSession;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\View\Result\Layout as ResultLayout;

class Send
{
    protected $multipleWishlistFactory;

    protected $_customerHelperView;

    /**
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    protected $inlineTranslation;

    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $_transportBuilder;

    /**
     * @var \Magento\Wishlist\Model\Config
     */
    protected $_wishlistConfig;

    /**
     * @var \Magenest\MultipleWishlist\Controller\MultipleWishlistProviderInterface
     */
    protected $mwishlistProvider;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Framework\Data\Form\FormKey\Validator
     */
    protected $_formKeyValidator;

    /**
     * @var WishlistSession
     */
    protected $wishlistSession;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\Controller\ResultFactory
     */
    protected $resultFactory;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $_url;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $_eventManager;

    /**
     * @param Action\Context $context
     * @param \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Wishlist\Controller\WishlistProviderInterface $wishlistProvider
     * @param \Magento\Wishlist\Model\Config $wishlistConfig
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     * @param \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
     * @param \Magento\Customer\Helper\View $customerHelperView
     * @param WishlistSession $wishlistSession
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Magento\Customer\Model\Session $customerSession,
        \Magenest\MultipleWishlist\Controller\MultipleWishlistProviderInterface $mwishlistProvider,
        \Magento\Wishlist\Model\Config $wishlistConfig,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Customer\Helper\View $customerHelperView,
        WishlistSession $wishlistSession,
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        \Magenest\MultipleWishlist\Model\MultipleWishlistFactory $multipleWishlistFactory
    ) {
        $this->_formKeyValidator = $formKeyValidator;
        $this->_customerSession = $customerSession;
        $this->mwishlistProvider = $mwishlistProvider;
        $this->_wishlistConfig = $wishlistConfig;
        $this->_transportBuilder = $transportBuilder;
        $this->inlineTranslation = $inlineTranslation;
        $this->_customerHelperView = $customerHelperView;
        $this->wishlistSession = $wishlistSession;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->resultFactory = $context->getResultFactory();
        $this->_url = $context->getUrl();
        $this->messageManager = $context->getMessageManager();
        $this->_eventManager = $context->getEventManager();
        $this->multipleWishlistFactory = $multipleWishlistFactory;
    }

    /**
     * Share wishlist
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     * @throws NotFoundException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function aroundExecute (\Magento\Wishlist\Controller\Index\Send $subject, callable $proceed)
    {
        $requestParams = $subject->getRequest()->getParams();

        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        if (!$this->_formKeyValidator->validate($subject->getRequest())) {
            $resultRedirect->setPath('*/*/');
            return $resultRedirect;
        }

        $wishlistId = $requestParams['wishlistId'];

        /** @var \Magenest\MultipleWishlist\Model\MultipleWishlist $wishlist */
        $wishlist = $this->multipleWishlistFactory->create()->load($wishlistId);
        if (!$wishlist) {
            throw new NotFoundException(__('Page not found.'));
        }

        $sharingLimit = $this->_wishlistConfig->getSharingEmailLimit();
        $textLimit = $this->_wishlistConfig->getSharingTextLimit();
        $emailsLeft = $sharingLimit - $wishlist->getShared();

        $emails = $subject->getRequest()->getPost('emails');
        $emails = empty($emails) ? $emails : explode(',', $emails);

        $error = false;
        $message = (string)$subject->getRequest()->getPost('message');
        if (strlen($message) > $textLimit) {
            $error = __('Message length must not exceed %1 symbols', $textLimit);
        } else {
            $message = nl2br(htmlspecialchars($message));
            if (empty($emails)) {
                $error = __('Please enter an email address.');
            } else {
                if (count($emails) > $emailsLeft) {
                    $error = __('This wish list can be shared %1 more times.', $emailsLeft);
                } else {
                    foreach ($emails as $index => $email) {
                        $email = trim($email);
                        if (!\Zend_Validate::is($email, 'EmailAddress')) {
                            $error = __('Please enter a valid email address.');
                            break;
                        }
                        $emails[$index] = $email;
                    }
                }
            }
        }

        if ($error) {
            $this->messageManager->addError($error);
            $this->wishlistSession->setSharingForm($subject->getRequest()->getPostValue());
            $resultRedirect->setPath('*/*/share?wishlist_id=' . $wishlistId);
            return $resultRedirect;
        }
        if ($wishlistId == '0')
        {
            return $proceed();
        }
        /** @var \Magento\Framework\View\Result\Layout $resultLayout */
        $resultLayout = $this->resultFactory->create(ResultFactory::TYPE_LAYOUT);
        $rssurl = $subject->getRequest()->getParam('rss_url');
        $this->addLayoutHandles($resultLayout, $rssurl);
        $this->inlineTranslation->suspend();

        $sent = 0;

        try {
            $customer = $this->_customerSession->getCustomerDataObject();
            $customerName = $this->_customerHelperView->getCustomerName($customer);

            $message .= $this->getRssLink($wishlist->getId(), $resultLayout, $rssurl);
            $emails = array_unique($emails);
            $sharingCode = $wishlist->getSharingCode();

            try {
                foreach ($emails as $email) {
                    $transport = $this->_transportBuilder->setTemplateIdentifier(
                        $this->scopeConfig->getValue(
                            'wishlist/email/email_template',
                            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                        )
                    )->setTemplateOptions(
                        [
                            'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                            'store' => $this->storeManager->getStore()->getStoreId(),
                        ]
                    )->setTemplateVars(
                        [
                            'customer' => $customer,
                            'customerName' => $customerName,
//                            'salable' => $wishlist->isSalable() ? 'yes' : '',
                            'items' => $this->getWishlistItems($resultLayout, $wishlistId),
                            'viewOnSiteLink' => $this->_url->getUrl('*/shared/index', ['code' => $sharingCode]),
                            'message' => $message,
                            'store' => $this->storeManager->getStore(),
                        ]
                    )->setFrom(
                        $this->scopeConfig->getValue(
                            'wishlist/email/email_identity',
                            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                        )
                    )->addTo(
                        $email
                    )->getTransport();

                    $transport->sendMessage();

                    $sent++;
                }
            } catch (\Exception $e) {
                $wishlist->setShared($wishlist->getShared() + $sent);
                $wishlist->save();
                throw $e;
            }
            $wishlist->setShared($wishlist->getShared() + $sent);
            $wishlist->save();

            $this->inlineTranslation->resume();

            $this->_eventManager->dispatch('wishlist_share', ['wishlist' => $wishlist]);
            $this->messageManager->addSuccess(__('Your wish list has been shared.'));
            $resultRedirect->setPath('*/*');
            return $resultRedirect;
        } catch (\Exception $e) {
            $this->inlineTranslation->resume();
            $this->messageManager->addError($e->getMessage());
            $this->wishlistSession->setSharingForm($subject->getRequest()->getPostValue());
            $resultRedirect->setPath('*/*/share?wishlist_id=' . $wishlistId);
            return $resultRedirect;
        }
    }

    /**
     * Prepare to load additional email blocks
     *
     * Add 'wishlist_email_rss' layout handle.
     * Add 'wishlist_email_items' layout handle.
     *
     * @param \Magento\Framework\View\Result\Layout $resultLayout
     * @return void
     */
    protected function addLayoutHandles(ResultLayout $resultLayout, $rssurl)
    {
        if ($rssurl) {
            $resultLayout->addHandle('wishlist_email_rss');
        }
        $resultLayout->addHandle('multiplewishlist_email_items');
    }

    /**
     * Retrieve RSS link content (html)
     *
     * @param int $wishlistId
     * @param \Magento\Framework\View\Result\Layout $resultLayout
     * @return mixed
     */
    protected function getRssLink($wishlistId, ResultLayout $resultLayout, $rssurl)
    {
        if ($rssurl) {
            return $resultLayout->getLayout()
                ->getBlock('wishlist.email.rss')
                ->setWishlistId($wishlistId)
                ->toHtml();
        }
    }

    /**
     * Retrieve wishlist items content (html)
     *
     * @param \Magento\Framework\View\Result\Layout $resultLayout
     * @return string
     */
    protected function getWishlistItems(ResultLayout $resultLayout, $wishlistId)
    {
        return $resultLayout->getLayout()
            ->getBlock('mwishlist.email.items')
            ->setWishlistId($wishlistId)
            ->toHtml();
    }

}