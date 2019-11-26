<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magenest\MultipleWishlist\Block;

/**
 * Wishlist Product Items abstract Block
 */
abstract class AbstractBlock extends \Magento\Catalog\Block\Product\AbstractProduct
{
    /**
     * Wishlist Product Items Collection
     *
     * @var \Magento\Wishlist\Model\ResourceModel\Item\Collection
     */
    protected $_collection;

    /**
     * Store wishlist Model
     *
     * @var \Magenest\MultipleWishlist\Model\MultipleWishlist
     */
    protected $_multipleWishlist;

    /**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;

    /**
     * @var \Magenest\MultipleWishlist\Helper\Data
     */
    protected $_multipleWishlistHelper;

    /**
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magenest\MultipleWishlist\Helper\Data $multipleWishlistHelper,
        array $data = []
    ) {
        $this->httpContext = $httpContext;
        $this->_multipleWishlistHelper = $multipleWishlistHelper;
        parent::__construct(
            $context,
            $data
        );
    }

    /**
     * Retrieve Wishlist Data Helper
     *
     * @return \Magenest\MultipleWishlist\Helper\Data
     */
    protected function _getHelper()
    {
        return $this->_multipleWishlistHelper;
    }

    /**
     * Retrieve Wishlist model
     *
     * @return \Magenest\MultipleWishlist\Model\MultipleWishlist
     */
    protected function _getWishlist()
    {
        return $this->_getHelper()->getWishlist();
    }

    /**
     * Prepare additional conditions to collection
     *
     * @param \Magenest\MultipleWishlist\Model\ResourceModel\Item\Collection $collection
     * @return \Magenest\MultipleWishlist\Block\Customer\MultipleWishlist
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function _prepareCollection($collection)
    {
        return $this;
    }

    /**
     * Create wishlist item collection
     *
     * @return \Magenest\MultipleWishlist\Model\ResourceModel\Item\Collection
     */
    protected function _createWishlistItemCollection()
    {
        return $this->_getWishlist()->getItemCollection();
    }


    /**
     * Retrieve wishlist instance
     *
     * @return \Magenest\MultipleWishlist\Model\MultipleWishlist
     */
    public function getWishlistInstance()
    {
        return $this->_getWishlist();
    }

}
