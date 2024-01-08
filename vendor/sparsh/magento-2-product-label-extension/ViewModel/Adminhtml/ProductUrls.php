<?php
/**
 * Sparsh ProductLabel Module
 * PHP version 8.2
 *
 * @category Sparsh
 * @package  Sparsh_ProductLabel
 * @author   Sparsh <magento@sparsh-technologies.com>
 * @license  https://www.sparsh-technologies.com  Open Software License (OSL 3.0)
 * @link     https://www.sparsh-technologies.com
 */
namespace Sparsh\ProductLabel\ViewModel\Adminhtml;

use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;

/**
 * Class Import
 * @package Knawat\Dropshipping\Block\Adminhtml
 */

#[\AllowDynamicProperties]
class ProductUrls implements ArgumentInterface
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $storeManager;

    /**
     * ProductUrls constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        StoreManagerInterface $storeManager
    ) {
        $this->storeManager = $storeManager;
    }

    /**
     * @param $path
     * @return mixed
     */
    public function getFileUrl()
    {
        $mediaUrl = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        return $fileUrl = $mediaUrl."sparsh/product_label_image/";
    }
}
