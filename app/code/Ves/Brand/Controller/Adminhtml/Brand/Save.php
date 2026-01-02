<?php
/**
 * Venustheme
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Venustheme.com license that is
 * available through the world-wide-web at this URL:
 * http://www.venustheme.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Venustheme
 * @package    Ves_Brand
 * @copyright  Copyright (c) 2014 Venustheme (http://www.venustheme.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
 */
namespace Ves\Brand\Controller\Adminhtml\Brand;

use Magento\Framework\App\Filesystem\DirectoryList;

class Save extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $_fileSystem;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\Filesystem $filesystem
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Backend\Helper\Js $jsHelper
        ) {
        $this->_fileSystem = $filesystem;
        $this->jsHelper = $jsHelper;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
    	return $this->_authorization->isAllowed('Ves_Brand::brand_save');
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
    	$data = $this->getRequest()->getPostValue();

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $model = $this->_objectManager->create('Ves\Brand\Model\Brand');

            $id = $this->getRequest()->getParam('brand_id');
            $isNew = true;
            $brand_image = $brand_thumbnail = "";
            if ($id) {
                $model->load($id);
                $brand_image = $model->getImage();
                $brand_thumbnail = $model->getThumbnail();
                $isNew = false;
            }

            /** @var \Magento\Framework\Filesystem\Directory\Read $mediaDirectory */
            $mediaDirectory = $this->_objectManager->get('Magento\Framework\Filesystem')
            ->getDirectoryRead(DirectoryList::MEDIA);
            $mediaFolder = 'ves/brand/';
            $path = $mediaDirectory->getAbsolutePath($mediaFolder);

            // Delete, Upload Image
            $imagePath = $mediaDirectory->getAbsolutePath($model->getImage());
            if(isset($data['image']['delete']) && file_exists($imagePath)){
                unlink($imagePath);
                $data['image'] = '';
                if($brand_image && $brand_thumbnail && $brand_image == $brand_thumbnail){
                    $data['thumbnail'] = '';
                }
            }
            if(isset($data['image']) && is_array($data['image'])){
                unset($data['image']);
            }
            if($image = $this->uploadImage('image')){
                
                $data['image'] = $image;
            }

            // Delete, Upload Thumbnail
            $thumbnailPath = $mediaDirectory->getAbsolutePath($model->getThumbnail());
            if(isset($data['thumbnail']['delete']) && file_exists($thumbnailPath)){
                unlink($thumbnailPath);
                $data['thumbnail'] = '';
                if($brand_image && $brand_thumbnail && $brand_image == $brand_thumbnail){
                    $data['image'] = '';
                }
            }
            if(isset($data['thumbnail']) && is_array($data['thumbnail'])){
                unset($data['thumbnail']);
            }
            if($thumbnail = $this->uploadImage('thumbnail')){
                $data['thumbnail'] = $thumbnail;
            }

            // Delete, Upload logo
            $brandPath = $mediaDirectory->getAbsolutePath($model->getBrandLogo());
            if(isset($data['brand_logo']['delete']) && file_exists($brandPath)){
                unlink($brandPath);
                $data['brand_logo'] = '';
            }
            if(isset($data['brand_logo']) && is_array($data['brand_logo'])){
                unset($data['brand_logo']);
            }
            if($brand_logo = $this->uploadImage('brand_logo')){
                $data['brand_logo'] = $brand_logo;
            }

            // Delete, Upload image one
            $onePath = $mediaDirectory->getAbsolutePath($model->getImageOne());
            if(isset($data['image_one']['delete']) && file_exists($onePath)){
                unlink($onePath);
                $data['image_one'] = '';
            }
            if(isset($data['image_one']) && is_array($data['image_one'])){
                unset($data['image_one']);
            }
            if($image_one = $this->uploadImage('image_one')){
                $data['image_one'] = $image_one;
            }

            // Delete, Upload image two
            $twoPath = $mediaDirectory->getAbsolutePath($model->getImageTwo());
            if(isset($data['image_two']['delete']) && file_exists($twoPath)){
                unlink($twoPath);
                $data['image_two'] = '';
            }
            if(isset($data['image_two']) && is_array($data['image_two'])){
                unset($data['image_two']);
            }
            if($image_two = $this->uploadImage('image_two')){
                $data['image_two'] = $image_two;
            }
            
            // Delete, Upload image three
            $threePath = $mediaDirectory->getAbsolutePath($model->getImageThree());
            if(isset($data['image_three']['delete']) && file_exists($threePath)){
                unlink($threePath);
                $data['image_three'] = '';
            }
            if(isset($data['image_three']) && is_array($data['image_three'])){
                unset($data['image_three']);
            }
            if($image_three = $this->uploadImage('image_three')){
                $data['image_three'] = $image_three;
            }
            
            // Delete, Upload image four
            $fourPath = $mediaDirectory->getAbsolutePath($model->getImageFour());
            if(isset($data['image_four']['delete']) && file_exists($fourPath)){
                unlink($fourPath);
                $data['image_four'] = '';
            }
            if(isset($data['image_four']) && is_array($data['image_four'])){
                unset($data['image_four']);
            }
            if($image_four = $this->uploadImage('image_four')){
                $data['image_four'] = $image_four;
            }
            
            // Delete, Upload image five
            $fivePath = $mediaDirectory->getAbsolutePath($model->getImageFive());
            if(isset($data['image_five']['delete']) && file_exists($fivePath)){
                unlink($fivePath);
                $data['image_five'] = '';
            }
            if(isset($data['image_five']) && is_array($data['image_five'])){
                unset($data['image_five']);
            }
            if($image_five = $this->uploadImage('image_five')){
                $data['image_five'] = $image_five;
            }
            
            // Delete, Upload image six
            $sixPath = $mediaDirectory->getAbsolutePath($model->getImageSix());
            if(isset($data['image_six']['delete']) && file_exists($sixPath)){
                unlink($sixPath);
                $data['image_six'] = '';
            }
            if(isset($data['image_six']) && is_array($data['image_six'])){
                unset($data['image_six']);
            }
            if($image_six = $this->uploadImage('image_six')){
                $data['image_six'] = $image_six;
            }
            
            // Delete, Upload image seven
            $sevenPath = $mediaDirectory->getAbsolutePath($model->getImageSeven());
            if(isset($data['image_seven']['delete']) && file_exists($sevenPath)){
                unlink($sevenPath);
                $data['image_seven'] = '';
            }
            if(isset($data['image_seven']) && is_array($data['image_seven'])){
                unset($data['image_seven']);
            }
            if($image_seven = $this->uploadImage('image_seven')){
                $data['image_seven'] = $image_seven;
            }
            
            // Delete, Upload image eight
            $eightPath = $mediaDirectory->getAbsolutePath($model->getImageEight());
            if(isset($data['image_eight']['delete']) && file_exists($eightPath)){
                unlink($eightPath);
                $data['image_eight'] = '';
            }
            if(isset($data['image_eight']) && is_array($data['image_eight'])){
                unset($data['image_eight']);
            }
            if($image_eight = $this->uploadImage('image_eight')){
                $data['image_eight'] = $image_eight;
            }

            if($data['url_key']=='')
            {
                $data['url_key'] = $data['name'];
            }
            $url_key = $this->_objectManager->create('Magento\Catalog\Model\Product\Url')->formatUrlKey($data['url_key']);
            $data['url_key'] = $url_key;

            $links = $this->getRequest()->getPost('links');
            $links = is_array($links) ? $links : [];
            if(!empty($links) && isset($links['related'])){
                $products = $this->jsHelper->decodeGridSerializedInput($links['related']);
                $data['products'] = $products;
            }

            $model->setData($data);
            try {
                $model->save();

                $this->_eventManager->dispatch(
                    'ves_brand_controller_brand_save',
                    [
                        'brand_id' => $model->getId(),
                        'is_new' => $isNew,
                        'brand' => $model
                    ]
                );

                $this->messageManager->addSuccess(__('You saved this brand.'));
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['brand_id' => $model->getId(), '_current' => true]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the brand.'));
            }
            $this->_getSession()->setFormData($data);
            return $resultRedirect->setPath('*/*/edit', ['brand_id' => $this->getRequest()->getParam('brand_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }

    public function uploadImage($fieldId = 'image')
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        if (isset($_FILES[$fieldId]) && $_FILES[$fieldId]['name']!='') 
        {
            $uploader = $this->_objectManager->create(
                'Magento\Framework\File\Uploader',
                array('fileId' => $fieldId)
                );
            $path = $this->_fileSystem->getDirectoryRead(
                DirectoryList::MEDIA
                )->getAbsolutePath(
                'catalog/category/'
                );

                /** @var \Magento\Framework\Filesystem\Directory\Read $mediaDirectory */
                $mediaDirectory = $this->_objectManager->get('Magento\Framework\Filesystem')
                ->getDirectoryRead(DirectoryList::MEDIA);
                $mediaFolder = 'ves/brand/';
                try {
                    $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png')); 
                    $uploader->setAllowRenameFiles(true);
                    $uploader->setFilesDispersion(false);
                    $result = $uploader->save($mediaDirectory->getAbsolutePath($mediaFolder)
                        );
                    return $mediaFolder.$result['name'];
                } catch (\Exception $e) {
                    $this->_logger->critical($e);
                    $this->messageManager->addError($e->getMessage());
                    return $resultRedirect->setPath('*/*/edit', ['brand_id' => $this->getRequest()->getParam('brand_id')]);
                }
            }
            return;
        }
    }