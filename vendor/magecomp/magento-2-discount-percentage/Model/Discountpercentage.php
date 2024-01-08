<?php
namespace Magecomp\Discountpercentage\Model;

use Magecomp\Discountpercentage\Api\DiscountpercentageInterface;
use Magento\Catalog\Model\ProductFactory;
    
class Discountpercentage implements DiscountpercentageInterface
{
    protected $productFactory;
 
    public function __construct(ProductFactory $productFactory)
    {
        $this->productFactory = $productFactory;
    }
    public function getDiscountPercentage($productid)
    {
        try {

            $product = $this->productFactory->create()->load($productid);
            $price = $product->getPrice();
            $specialprice = $product->getSpecialPrice();
            $productname = $product->getName();
            if ($specialprice !== null) {
                $discountprice = round(($specialprice/$price)*100);
                $percentageconversion = 100 - $discountprice;
                return [["status"=>true, "message"=>__("Discount is : ".$percentageconversion."% off for ".$productname)]];
            } else {
                return [["status"=>false, "message"=>__("The Product does not have Discount Price!")]];
            }
        } catch (\Exception $e) {
            return [["status"=>false, "message"=>$e->getMessage()]];
        }
    }
}
