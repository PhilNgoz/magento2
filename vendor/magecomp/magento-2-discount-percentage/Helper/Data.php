<?php
namespace Magecomp\Discountpercentage\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{

    protected $scopeConfig;
    protected $_storeManager;
    protected $productFactory;
    const DISCOUNTPERCENTAGE_MODULEOPTION_ENABLE = 'discountpercentage/general/enable';

    public function __construct(
        Context $context,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->_storeManager = $storeManager;
        $this->productFactory = $productFactory;

        parent::__construct($context);
    }

    public function isActive()
    {
        return $this->scopeConfig->getValue(
            self::DISCOUNTPERCENTAGE_MODULEOPTION_ENABLE,
            ScopeInterface::SCOPE_STORE,
            $this->getStoreId()
        );
    }

    public function getStoreId()
    {
        return $this->_storeManager->getStore()->getId();
    }

    public function getPriceById($id)
    {
        $product = $this->productFactory->create();
        $productPriceById = $product->load($id)->getPrice();
        return $productPriceById;
    }
}
