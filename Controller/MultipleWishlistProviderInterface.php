<?php

namespace Magenest\MultipleWishlist\Controller;

interface MultipleWishlistProviderInterface
{
    /**
     * Retrieve wishlist
     *
     * @param string $wishlistId
     * @return \Magenest\MultipleWishlist\Model\MultipleWishlist
     */
    public function getWishlist($id = null);
}
