<?php
/**
 * Created by PhpStorm.
 * User: chung
 * Date: 2/12/18
 * Time: 11:32 AM
 */

namespace Magenest\MultipleWishlist\Plugin\Customer;

/**
 * Class SectionLoad
 * @package Magenest\MultipleWishlist\Plugin\Customer
 */
class SectionLoad
{
    /**
     * @var \Magenest\MultipleWishlist\Model\MultipleWishlistFactory
     */
    protected $multipleWishlistFactory;

    /**
     * SectionLoad constructor.
     * @param \Magenest\MultipleWishlist\Model\MultipleWishlistFactory $multipleWishlistFactory
     */
    public function __construct(
        \Magenest\MultipleWishlist\Model\MultipleWishlistFactory $multipleWishlistFactory
    )
    {
        $this->multipleWishlistFactory = $multipleWishlistFactory;
    }

    /**
     * @param \Magento\Customer\CustomerData\SectionPoolInterface $subject
     * @param $result
     * @return mixed
     */
    public function afterGetSectionsData(
        \Magento\Customer\CustomerData\SectionPoolInterface $subject,
        $result
    )
    {
        if (isset($result['wishlist'])) {
            /** @var \Magento\Framework\Phrase $currentCounter */
            if ($result['wishlist']['counter'] !== null) {
                $currentCounter = $result['wishlist']['counter'];
                $args = $currentCounter->getArguments();
                $text = $currentCounter->getText();
                if ($text == "1 item") {
                    $args[0] = 1;
                }
            } else {
                $args = [0];
            }

            $count = $this->multipleWishlistFactory->create()->getAllItemsCount();
            $result['wishlist']['counter'] = __("%1 item(s)", $args[0] + $count);
        }
        return $result;
    }
}