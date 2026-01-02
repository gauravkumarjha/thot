<?php
/**
 * @category Mageants GiftCard
 * @package Mageants_GiftCard
 * @copyright Copyright (c) 2016 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\GiftCard\Controller\Adminhtml\Index;

use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Backend\Model\Session;
use Mageants\GiftCard\Model\Codeset;

/**
 * AddCodeSet class for add new Codeset
 */
class Addcodeset extends \Magento\Backend\App\Action
{
    /**
     * @var Mageants\GiftCard\Model\Codeset
     */
    public $codeset;

    /**
     * @var Magento\Framework\Registry
     */
    public $_coreRegistry;

    /**
     * @var Magento\Backend\Model\Session
     */
    protected $session;

    /**
     * @param Context $context
     * @param Session $session
     * @param Codeset $codeset
     * @param Registry $coreRegistry
     */
    public function __construct(
        Context $context,
        Session $session,
        Codeset $codeset,
        Registry $coreRegistry
    ) {
        parent::__construct($context);
        $this->_coreRegistry = $coreRegistry;
        $this->session = $session;
        $this->codeset = $codeset;
    }
    
    /**
     * Perform MassDele Action
     */
    public function execute()
    {
        $codesetid = (int) $this->getRequest()->getParam('code_set_id');
        $this->session->setCodeId($codesetid);
        $codesetData = $this->codeset;
        
        if ($codesetid) {
            $codesetData = $codesetData->load($codesetid);
            $templateTitle = $codesetData->getQuestion();
            if (!$codesetData->getCodeSetId()) {
                $this->messageManager->addError(__('Codeset no longer exist.'));
                $this->_redirect('giftcertificate/index/');
                return;
            }
        }
        $this->_coreRegistry->register('code_set_data', $codesetData);
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $title = $codesetid ? __('Edit Code Set').$templateTitle : __('Add Code Set');
        $resultPage->getConfig()->getTitle()->prepend($title);
        return $resultPage;
    }
}
