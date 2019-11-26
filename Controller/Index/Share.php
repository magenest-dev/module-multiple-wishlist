<?php
/**
 * Created by PhpStorm.
 * User: heomep
 * Date: 29/03/2017
 * Time: 11:00
 */

namespace Magenest\MultipleWishlist\Controller\Index;

use Magento\Framework\App\Action;
use Magento\Framework\Controller\ResultFactory;

class Share extends \Magenest\MultipleWishlist\Controller\AbstractIndex
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->customerSession = $customerSession;
        parent::__construct($context);
    }

    /**
     * Prepare wishlist for share
     *
     * @return void|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        if ($this->customerSession->authenticate()) {
            /** @var \Magento\Framework\View\Result\Page $resultPage */
            $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
           // $resultPage->getConfig()->set
            return $resultPage;
        }
    }
}
