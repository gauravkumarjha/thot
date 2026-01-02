<?php

namespace Magecomp\Gstcharge\Controller\Adminhtml\Gstreport;

use Magento\Backend\Block\Widget\Grid\ExportInterface;
use Magento\Framework\App\Filesystem\DirectoryList;

class ExportCsv extends \Magecomp\Gstcharge\Controller\Adminhtml\Gstreport
{
    public function execute()
    {
        $this->_view->loadLayout();
        $fileName = 'gst_reports.csv';
        /** @var ExportInterface $exportBlock */
        $exportBlock = $this->_view->getLayout()->getChildBlock('adminhtml.report.grid', 'grid.export');
        return $this->_fileFactory->create(
            $fileName,
            $exportBlock->getCsvFile(),
            DirectoryList::VAR_DIR
        );
    }
}
