<?php

namespace Magenest\MultipleWishlist\Controller\Index;

use Magento\Framework\App\Action\Context;


/**
 * Class Search
 * @package Magenest\MultipleWishlist\Controller\Index
 */
class Search extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $_resultJsonFactory;
    /**
     * @var \Magenest\MultipleWishlist\Model\MultipleWishlistFactory
     */
    protected $multipleWishlistFactory;
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $jsonFactory;
    /**
     * @var \Magento\Framework\Serialize\SerializerInterface
     */
    protected $serializerInterface;

    /**
     * Search constructor.
     * @param Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magenest\MultipleWishlist\Model\MultipleWishlistFactory $multipleWishlistFactory
     * @param \Magento\Framework\Controller\Result\JsonFactory $jsonFactory
     * @param \Magento\Framework\Serialize\SerializerInterface $serializer
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magenest\MultipleWishlist\Model\MultipleWishlistFactory $multipleWishlistFactory,
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
        \Magento\Framework\Serialize\SerializerInterface $serializer
    )
    {
        $this->multipleWishlistFactory = $multipleWishlistFactory;
        $this->_resultJsonFactory = $resultJsonFactory;
        $this->jsonFactory = $jsonFactory;
        $this->serializerInterface = $serializer;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Json|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $nameSearch = $this->getRequest()->getParam('nameSearch');
        $customerId = $this->getRequest()->getParam('customerId');
        $collection = $this->multipleWishlistFactory->create()->getCollection()
            ->addFieldToFilter("name", array("like" => "%$nameSearch%"))
            ->addFieldToFilter('customer_id', array('eq' => $customerId))
            ->setOrder('name', 'ASC')
            ->getData();
        $json = $this->serializerInterface->serialize($collection);
        $return = $this->jsonFactory->create()->setData($json);
        return $return;
    }
}
