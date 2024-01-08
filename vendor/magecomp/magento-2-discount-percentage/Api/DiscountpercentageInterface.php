<?php
namespace Magecomp\Discountpercentage\Api;

/**
 * Interface Discountpercentage
 * Magecomp\Discountpercentage\Api
 */
interface DiscountpercentageInterface
{
    /**
     * Generate Discount percentage for special priced product
     *
     * @api
     * @param string $productid
     * @return string
     */
    public function getDiscountPercentage($productid);
}
