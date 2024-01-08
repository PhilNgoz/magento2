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

namespace Sparsh\ProductLabel\Controller\Adminhtml\Index;

use Sparsh\ProductLabel\Model\ProductLabels;
use Magento\Backend\App\Action;

/**
 * Class Delete
 *
 * @category Sparsh
 * @package  Sparsh_ProductLabel
 * @author   Sparsh <magento@sparsh-technologies.com>
 * @license  https://www.sparsh-technologies.com  Open Software License (OSL 3.0)
 * @link     https://www.sparsh-technologies.com
 */

#[\AllowDynamicProperties] 
class Delete extends Action
{
    /**
     * @var ProductLabels
     */
    protected $model;

    /**
     * Delete constructor.
     * @param Action\Context $context
     * @param ProductLabels $model
     */
    public function __construct(
        Action\Context $context,
        ProductLabels $model
    ) {
        $this->model = $model;
        parent::__construct($context);
    }

    /**
     * Delete action
     *
     * @return $this
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $resultRedirect = $this->resultRedirectFactory->create();

        if ($id) {
            try {
                $this->model->load($id);
                $this->model->delete();
                $this->messageManager->addSuccessMessage(__('Product rule has been deleted successfully.'));
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['id' => $id]);
            }
        }
        $this->messageManager->addErrorMessage(__('We can\'t find product rule to delete.'));
        return $resultRedirect->setPath('*/*/');
    }
}
