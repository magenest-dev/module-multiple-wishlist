<?php

namespace Magenest\MultipleWishlist\Cron;

class Sendmail
{
    protected $_request;
    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $_transportBuilder;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    protected $helper;

    protected $scopeConfig;

    public function __construct(
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magenest\MultipleWishlist\Helper\Data $helper,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    )
    {
        $this->_request = $request;
        $this->_transportBuilder = $transportBuilder;
        $this->_storeManager = $storeManager;
        $this->helper = $helper;
        $this->scopeConfig = $scopeConfig;
    }

    public function execute()
    {
        $enableModule = $this->scopeConfig->getValue('multiplewishlist/general/active', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if($enableModule == 1){
            $templateLayout = 'multiplewishlist_test_template';
            $customerData = $this->helper->getAllCustomer();
            foreach ($customerData as $value)
            {
                $customerId = $value['entity_id'] ;
                $collectionWishList = $this->helper->getProductInMain($customerId);
                $templateVars = array(
                    'store' => $this->_storeManager->getStore(),
                    'customer' => $value['entity_id'],
                    'name' => $value['firstname'],
                    'gmail' => $value['email'],
                    'collectionWishList' =>$collectionWishList,
                );
                if($value['email'] && $collectionWishList->count()>0){
                    $this->helper->sendMail($value['email'], $templateVars, $templateLayout);
                }
            }
        }
    }

}