<?php

namespace Magenest\MultipleWishlist\Block\Share;


use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Pricing\Helper\Data as PricingHelper;
use Magento\Framework\Registry;
use Magento\Store\Model\ScopeInterface;

/**
 * Class TagSocialShare
 * @package Magenest\MultipleWishlist\Block\Share
 */
class TagSocialShare extends \Magenest\MultipleWishlist\Block\AbstractBlock
{
    /**
     * @var \Magento\Theme\Block\Html\Header\Logo
     */
    protected $_logo;

    protected $helper;
    /**
     * TagSocialShare constructor.
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param \Magenest\MultipleWishlist\Helper\Data $multipleWishlistHelper
     * @param \Magento\Theme\Block\Html\Header\Logo $logo
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Theme\Block\Html\Header\Logo $logo,
        \Magenest\MultipleWishlist\Helper\Data $helper,
        array $data)
    {
        $this->_logo = $logo;
        $this->helper = $helper;
        parent::__construct($context, $httpContext, $helper, $data);
    }

    /**
     * @return string
     */
    public function getLogoSrc()
    {
        return $this->_logo->getLogoSrc();
    }

    public function getWishListId()
    {
        $wishlistId = $this->helper->getWishlistId();
        return $wishlistId;
    }

    public function getWishlistItems($wishListId)
    {
        $item = $this->helper->getWishlistItems($wishListId);
        return $item;
    }

}
