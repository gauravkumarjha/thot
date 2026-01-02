<?php

declare(strict_types=1);

namespace Chetaru\Edit\Controller\Adminhtml\Post;

use Chetaru\Edit\Model\PostFactory;
use Chetaru\Edit\Model\ResourceModel\Post as PostResource;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;

class Save extends Action implements HttpPostActionInterface
{
    public function __construct(
        Context $context,
        private PostResource $resource,
        private PostFactory $postFactory
    ) {
        parent::__construct($context);
    }

    public function execute(): ResultInterface
    {
        $data = $this->getRequest()->getPostValue();

        if(isset($data['categories']) && !empty($data['categories'])){
			$data['categories']=json_encode($data['categories']);
		}

        if(isset($data['url_key']) && !empty($data['url_key'])){
			$data['url_key']=$this->_clean($data['url_key']);
		}else{
			$data['url_key']=$this->_clean($data['name']);
		}

        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $model = $this->postFactory->create();
            if (empty($data['post_id'])) {
                $data['post_id'] = null;
            }

            //echo "<pre>";  print_r($data); echo "</pre>";

            $data = $this->_filterFoodData($data);
            $model->setData($data);

            try {
                $this->resource->save($model);
                $this->messageManager->addSuccessMessage(__('You saved the post.'));
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $exception) {
                $this->messageManager->addExceptionMessage($exception);
            } catch (\Throwable $e) {
               $this->messageManager->addErrorMessage(__('Something went wrong while saving the post.'. $e));
            }
        }

        return $resultRedirect->setPath('*/*/');
    }

    protected function _clean($str, $replace = array(), $delimiter = '-')
	{
		setlocale(LC_ALL, 'en_US.UTF8');
		if (!empty($replace)) {
			$str = str_replace((array)$replace, ' ', $str);
		}

		$clean = iconv('UTF-8', 'ASCII//TRANSLIT', $str);
		$clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $clean);
		$clean = strtolower(trim($clean, '-'));
		$clean = preg_replace("/[\/_|+ -]+/", $delimiter, $clean);

		return $clean;

	}
    public function _filterFoodData(array $rawData)
    {
        $data = $rawData;
        if (isset($data['featured_image'][0]['name'])) {
            $data['featured_image'] = $data['featured_image'][0]['url'];
        } else {
            $data['featured_image'] = null;
        }
        if (isset($data['logo'][0]['name'])) {
            $data['logo'] = $data['logo'][0]['url'];
        } else {
            $data['logo'] = null;
        }
        if (isset($data['banner_image'][0]['name'])) {
            $data['banner_image'] = $data['banner_image'][0]['url'];
        } else {
            $data['banner_image'] = null;
        }
        if (isset($data['brand_page_image'][0]['name'])) {
            $data['brand_page_image'] = $data['brand_page_image'][0]['url'];
        } else {
            $data['brand_page_image'] = null;
        }
        return $data;
    }
}
