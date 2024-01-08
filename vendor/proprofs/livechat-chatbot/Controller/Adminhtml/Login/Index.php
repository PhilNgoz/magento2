<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace ProProfs\Chat\Controller\Adminhtml\login;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;


/**
 * Class Index
 */
class Index extends Action implements HttpGetActionInterface
{
    const MENU_ID = 'ProProfs_Chat::greetings_login';

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;


    /**
     * Index constructor.
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
		
    ) {
		
        parent::__construct($context);

        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Load the page defined in view/adminhtml/layout/Chat_login_index.xml
     *
     * @return Page
     */
    public function execute()
    {
	
		$post = $this->getRequest()->getParam('ProProfsAccount');
		
        $resultPage = $this->resultPageFactory->create();
		
        $resultPage->setActiveMenu(static::MENU_ID);
        $resultPage->getConfig()->getTitle()->prepend(__('ProProfs Chat'));
		if($post!=''){
			$this->ProPorfsChatInstallation($post);
		}
		
        return $resultPage;
    }
	protected function ProPorfsChatInstallation($Siteid){
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
		$connection = $resource->getConnection();
		$tableName = $resource->getTableName('core_config_data');
		$sql = $connection->select('value')->from($tableName)->where('path = ?', 'design/footer/absolute_footer') ;
		$result = $connection->fetchAll($sql);
		$chatCode="<script>(function(){var pp=document.createElement('script'), ppr=document.getElementsByTagName('script')[0]; stid='".$Siteid."';pp.type='text/javascript'; pp.async=true; pp.src=('https:' == document.location.protocol ? 'https://' : 'http://') + 'dev.live2support.com/dashboardv2/chatwindow/'; ppr.parentNode.insertBefore(pp, ppr);})();</script>";
		foreach($result as $r)
		{
			//$updatedValue=$r['value'].$chatCode;
			$updatedValue=$chatCode;
			$sql = 'Update core_config_data Set value = "'.$updatedValue.'" where config_id = "'.$r['config_id'].'" LIMIT 1';
			$connection->exec($sql);

			
		}
	}
	
}
