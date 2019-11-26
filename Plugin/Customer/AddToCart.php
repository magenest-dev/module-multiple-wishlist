<?php
namespace Magenest\MultipleWishlist\Plugin\Customer;

class AddToCart
{

    public function  beforeAddToCart(\Magento\Wishlist\Model\Item $subject, $cart, $delete)
    {
          $delete = false;
          return [$cart, $delete];
    }
}