<?php
/**
 * Created by PhpStorm.
 * User: chung
 * Date: 2/12/18
 * Time: 11:32 AM
 */

namespace Magenest\MultipleWishlist\Plugin\Customer;

class RemindEmailRule
{
    protected $multipleWishlistFactory;
    protected $request;
    protected $ruleModelFactory;
    protected $collectionRuleFactory;
    protected $helper;

    public function __construct(
        \Magenest\MultipleWishlist\Model\MultipleWishlistFactory $multipleWishlistFactory,
        \Magento\Framework\Webapi\Rest\Request $request,
        \Magento\CatalogRule\Model\RuleFactory $ruleModelFactory,
        \Magento\CatalogRule\Model\ResourceModel\Rule\CollectionFactory $collectionRuleFactory,
        \Magenest\MultipleWishlist\Helper\Data $helper
    )
    {
        $this->multipleWishlistFactory = $multipleWishlistFactory;
        $this->request = $request;
        $this->ruleModelFactory = $ruleModelFactory;
        $this->collectionRuleFactory = $collectionRuleFactory;
        $this->helper = $helper;
    }

    /**
     * @param \Magento\Customer\CustomerData\SectionPoolInterface $subject
     * @param $result
     * @return mixed
     */
    public function afterExecute(\Magento\CatalogRule\Controller\Adminhtml\Promo\Catalog\Save $save, $result)
    {
        $data = $this->request->getParams();
        if (isset($data['auto_apply'])&&isset($data['is_active'])) {
            $dataLastRule = $this->collectionRuleFactory->create()->getLastItem();
            $productIsSaled = $this->helper->getSpecialProductData();
            $ruleId = $dataLastRule->getRule_id();
            $simple_Action = $dataLastRule->getSimpleAction();//Action
            $customerGroup = $dataLastRule->getData('customer_group_ids')[0];//Group customer
            $discount_Amount= $dataLastRule->getDiscountAmount();//Value Action
        }
    }
}