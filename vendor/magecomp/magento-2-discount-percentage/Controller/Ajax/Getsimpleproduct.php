<?php
namespace Magecomp\Discountpercentage\Controller\Ajax;

use Magento\Framework\App\Action;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Catalog\Model\Product;
use Magento\Framework\View\Result\Page;

class Getsimpleproduct extends Action\Action
{
    public $_resultJsonFactory;
    protected $_product;
    protected $_page;
    protected $productFactory;

    public function __construct(
        Action\Context $context,
        Product $product,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        Page $page,
        JsonFactory $resultJsonFactory
    ) {
        $this->_resultJsonFactory   = $resultJsonFactory;
        $this->_product             = $product;
        $this->productFactory = $productFactory;
        $this->_page                = $page;
        return parent::__construct($context);
    }

    public function execute()
    {

        $result = $this->_resultJsonFactory->create();
        if ($this->getRequest()->isAjax()) {
            $id = $this->getRequest()->getParam('simpleproductid');
            $product = $this->productFactory->create();
            $productPriceById = $product->load($id)->getPrice();
            $productFinalPriceById = $product->load($id)->getFinalPrice();

            if ($productFinalPriceById != $productPriceById) {
                $_savePercent = 100 - round(((float)$productFinalPriceById / (float)$productPriceById) * 100);
                $displaypercentage = $_savePercent . '% off';
                return $result->setData(['request' => 'Ok', 'displaypercentage' => $displaypercentage]);
            } else {
                return $result->setData(['request' => 'Ok', 'displaypercentage' => ""]);
            }
            
            
        } else {
            return $result->setData(['request' => 'AJAX ERROR']);
        }
    }
}
