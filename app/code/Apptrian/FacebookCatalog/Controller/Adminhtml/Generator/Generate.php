<?php
/**
 * @category  Apptrian
 * @package   Apptrian_FacebookCatalog
 * @author    Apptrian
 * @copyright Copyright (c) Apptrian (http://www.apptrian.com)
 * @license   http://www.apptrian.com/license Proprietary Software License EULA
 */

namespace Apptrian\FacebookCatalog\Controller\Adminhtml\Generator;

class Generate extends \Magento\Backend\App\Action
{
    /**
     * @var \Apptrian\FacebookCatalog\Helper\Data
     */
    public $dataHelper;
    
    /**
     * Constructor
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Apptrian\FacebookCatalog\Helper\Data $dataHelper
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Apptrian\FacebookCatalog\Helper\Data $dataHelper
    ) {
        $this->dataHelper = $dataHelper;
        
        parent::__construct($context);
    }
    
    /**
     * Optimize action.
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        set_time_limit(18000);
        
        try {
            $this->dataHelper->generate();
            
            $this->messageManager->addSuccess(
                __('Product feed generation completed successfully.')
            );
        } catch (\Exception $e) {
            $message = __('Product feed generation failed.');
            $this->messageManager->addError($message);
            $this->messageManager->addError($e->getMessage());
        }
        
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        
        return $resultRedirect->setPath(
            'adminhtml/system_config/edit',
            ['section' => 'apptrian_facebookcatalog']
        );
    }
}
