<?php

namespace Magenest\MultipleWishlist\Observer\Layout;

use Magento\Framework\Event\ObserverInterface;

class Load implements ObserverInterface
{
    protected $scopeConfig;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    )
    {
        $this->scopeConfig = $scopeConfig;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $fullActionName = $observer->getEvent()->getFullActionName();
        /* @var \Magento\Framework\View\Layout $layout */
        $layout = $observer->getEvent()->getLayout();
        $enableModule = $this->scopeConfig->getValue('multiplewishlist/general/active', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $handler = '';
        if (($fullActionName == 'wishlist_index_index') && $enableModule==1) {
        $handler = 'multiplewishlist_index_index';
        }

        if ($handler) {
        $layout->getUpdate()->addHandle($handler);
        }
    }
}
