<?php
/**
 * Created by PhpStorm.
 * User: chung
 * Date: 7/21/17
 * Time: 1:30 PM
 */

namespace Magenest\MultipleWishlist\Controller\Customer;

use Magento\Framework\App\Action\Context;

/**
 * Class Check
 * @package Magenest\MultipleWishlist\Controller\Customer
 */
class Check extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_session;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $_resultJsonFactory;

    /**
     * Check constructor.
     * @param Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     */
    public function __construct(
        Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    )
    {
        $this->_resultJsonFactory = $resultJsonFactory;
        $this->_session = $customerSession;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Json|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $return = $this->_resultJsonFactory->create();
        $return->setData('true');
        if (!$this->_session->isLoggedIn()) {
            $return->setData('false');
        }
        return $return;
    }
}