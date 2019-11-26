<?php

namespace Magenest\MultipleWishlist\Observer;
use Magento\Framework\App\Config\ScopeConfigInterface;

class AfterSaveStoreConfig implements \Magento\Framework\Event\ObserverInterface
{
    protected $_configWriter;

    protected $_scopeConfig;

    protected $request;

    public function __construct(
        \Magento\Framework\App\Config\Storage\WriterInterface $configWriter,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\RequestInterface $request
)
    {
        $this->_configWriter = $configWriter;
        $this->_scopeConfig = $scopeConfig;
        $this->request = $request;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $valueConfig = $this->request->getParam('groups');
        if(isset($valueConfig['cronsendmail']))
        {
            $cronSendMail = $valueConfig['cronsendmail']['fields'];
            if(isset($cronSendMail['time']['value'])){
                $number = $cronSendMail['time']['value'] ;
            }elseif (isset($cronSendMail['time']['inherit']))
            {
                $number = $this->_scopeConfig->getValue('multiplewishlist/cronsendmail/time', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            }
            if(isset($cronSendMail['frequency']['value'])){
                $frequency = $cronSendMail['frequency']['value'];
            }elseif (isset($cronSendMail['frequency']['inherit']))
            {
                $frequency = $this->_scopeConfig->getValue('multiplewishlist/cronsendmail/frequency', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            }
            if($frequency == 'D')
            {
                $shedule = (int)$number;
            }elseif ($frequency == 'W')
            {
                $shedule = (int)$number*7;
            }else{
                $shedule = (int)$number*30;
            }
            $this->_configWriter->save('multiplewishlist/cronsendmail/schedule', $shedule, $scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT, $scopeId = 0);
        }
    }
}
