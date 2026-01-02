<?php

namespace Magecomp\Gstcharge\Model\Config\Backend;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\ReadFactory;
use Magento\Framework\Filesystem\DirectoryList;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Store\Model\StoreManagerInterface;

class Importgst extends \Magento\Framework\App\Config\Value
{
    protected $_dbConnection;
    protected $_messageManager;
    private $productRepository;

    public function __construct(
        Context $context,
        Registry $registry,
        ScopeConfigInterface $config,
        TypeListInterface $cacheTypeList,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        Filesystem $filesystem,
        ReadFactory $readFactory,
        ResourceConnection $connection,
        ManagerInterface $messageManager,
        ProductFactory $productRepository,
        StoreManagerInterface $storeManager,
        Product $productObj,
        $data = []
    ) {
        $this->_filesystem = $filesystem;
        $this->_readFactory = $readFactory;
        $this->_dbConnection = $connection;
        $this->_messageManager = $messageManager;
        $this->productRepository = $productRepository;
        $this->_storeManager = $storeManager;
        $this->productObj = $productObj;
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
    }

    public function afterSave()
    {


        try {
            if (strpos($this['fieldset_data']['importcsv']['type'], 'excel') === false) {
                return $this;
            }


            $uploaders = ObjectManager::getInstance()->create('Magento\MediaStorage\Model\File\Uploader', ['fileId' => $this['fieldset_data']['importcsv']]);
            $uploadvalue = $uploaders->validateFile();
            $csvFile = $uploadvalue['tmp_name'];

            $tmpDirectory = ini_get('upload_tmp_dir') ? $this->_readFactory->create(ini_get('upload_tmp_dir'))
                : $this->_filesystem->getDirectoryRead(DirectoryList::SYS_TMP);

            $path = $tmpDirectory->getRelativePath($csvFile);
            $stream = $tmpDirectory->openFile($path);
            $headers = $stream->readCsv();
            if ($headers === false || count($headers) < 2) {
                $stream->close();
                throw new \Magento\Framework\Exception\LocalizedException(__('Please correct csv File Format.'));
            }
            $arrayColumn = 0;
            $rowNumber = 0;
            $twodarray = [];

            while (false !== ($csvLine = $stream->readCsv())) {
                $rowNumber++;
                if ($csvLine[0] == '' || $csvLine[1] == '' || $csvLine[2] == '') {
                    $this->_messageManager->addErrorMessage('Fill up all the data in csv at row number # ' . $rowNumber);
                    return parent::afterSave();
                } else {

                    $twodarray[$arrayColumn][0] = $csvLine[0];
                    $twodarray[$arrayColumn][1] = $csvLine[1];
                    $twodarray[$arrayColumn][2] = $csvLine[2];
                    $arrayColumn++;
                }
            }
            $totalRow = $this->_saveImportData($twodarray, $arrayColumn);
            $stream->close();
            $this->_messageManager->addSuccess(__("Successfully Imported Gst Rate In " . $totalRow . " Products"));
            return parent::afterSave();
        } catch (\Exception $e) {
            $this->_messageManager->addErrorMessage($e->getMessage());
            return parent::afterSave();
        }
    }

    public function _saveImportData($data, $arrayColumn)
    {
        try {
            $totalRow = 0;
            for ($i = 0; $i < $arrayColumn; $i++) {

                $productId = $this->productObj->getIdBySku($data[$i][0]);
                if ($productId && $productId > 0) {
                    $product = $this->productRepository->create()->load($productId);
                    if ($product) {
                        $product->setHsncode($data[$i][1]);
                        $product->setGstSource($data[$i][2]);
                        $product->setStoreId(0);
                        $product->save();
                        $totalRow++;
                    }
                }

            }
            return $totalRow;
        } catch (\Exception $e) {
            $this->_logger->info($e->getMessage());
        }
    }
}
