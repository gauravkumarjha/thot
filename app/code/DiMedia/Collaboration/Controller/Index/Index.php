<?php

namespace DiMedia\Collaboration\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Exception\FileSystemException;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

class Index extends Action
{
    protected $formKeyValidator;
    protected $transportBuilder;
    protected $messageManager;
    protected $resource;
    protected $resultRedirectFactory;
    protected $uploaderFactory;
    protected $filesystem;
    protected $storeManager;
    protected $logger;

    public function __construct(
        Context $context,
        Validator $formKeyValidator,
        ResourceConnection $resource,
        LoggerInterface $logger,
        TransportBuilder $transportBuilder,
        RedirectFactory $resultRedirectFactory,
        \Magento\MediaStorage\Model\File\UploaderFactory $uploaderFactory,
        \Magento\Framework\Filesystem $filesystem,
        StoreManagerInterface $storeManager,
        ManagerInterface $messageManager
    ) {
        $this->formKeyValidator = $formKeyValidator;
        $this->transportBuilder = $transportBuilder;
        $this->messageManager = $messageManager;
        $this->resource = $resource;
        $this->resultRedirectFactory = $resultRedirectFactory;
        $this->uploaderFactory = $uploaderFactory;
        $this->filesystem = $filesystem;
        $this->storeManager = $storeManager;
        $this->logger = $logger;
        parent::__construct($context);
    }
    public function execute()
    {
        //echo "test";
        if (!$this->formKeyValidator->validate($this->getRequest())) {
            $this->messageManager->addErrorMessage(__('Invalid form key.'));
        } else {
            $this->messageManager->addSuccessMessage(__('Thank you for your submission.'));
        }
        $post = $this->getRequest()->getPostValue();

        if (!$post) {
            $this->__redirect('*/*/');
            return;
        }


        
        if (!empty($post)) {
            // Debugging: Uncomment the following lines for development
            // print_r($post);
            // die();
           
            try {
            $name  = isset($post['name']) ? $this->trim_input($post['name']) : "";
            $email = isset($post['email']) ? $this->trim_input($post['email']) : "";
            $phone = isset($post['phone']) ? $this->trim_input($post['phone']) : "";
            $message = isset($post['message']) ? $this->trim_input($post['message']) : "";
            $catalog = isset($post['catalog']) ? $this->trim_input($post['catalog']) : "";
            $error= false;
            if(empty($name)) {
                    $this->messageManager->addErrorMessage(__('Please enter your name'));
                    $error = true;
            } else if(empty($email)) {
                    $this->messageManager->addErrorMessage(__('Please enter your email'));
                    $error = true;
            } else if (empty($phone)) {
                    $this->messageManager->addErrorMessage(__('Please enter your phone'));
                    $error = true;
            } else if (empty($message)) {
                    $this->messageManager->addErrorMessage(__('Please enter your message'));
                    $error = true;
            }
            $filePath = ""; 
            if (isset($_FILES['catalog']['name']) && $_FILES['catalog']['tmp_name']) {
                    // Create uploader instance
                    $uploader = $this->uploaderFactory->create(['fileId' => 'catalog']);

                    // Validate allowed file extensions (optional)
                    $uploader->setAllowedExtensions(['jpg', 'jpeg', 'png', 'pdf', 'docx']);

                    // Set whether to overwrite existing files
                    $uploader->setAllowRenameFiles(true);

                    // Specify the upload directory
                    $uploadPath = $this->filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA)
                        ->getAbsolutePath('catalog_uploads/');

                    // Save the file to the specified path
                    $result = $uploader->save($uploadPath);
                    // print_r($result);
                    // die();
                    if ($result) {
                        // Get file details
                        $fileName = $result['file'];
                        $fileType = $result['type']; // MIME type of the file
                        $filePath = $uploadPath . $fileName;
                        $filePath = str_replace($_SERVER['DOCUMENT_ROOT'],"https://magento-878717-4602051.cloudwaysapps.com/", $filePath);
                        // Output the file details
                        // $this->messageManager->addSuccessMessage(__('File uploaded successfully.'));
                        // echo "File Name: " . $fileName . "<br>";
                        // echo "File Type: " . $fileType . "<br>";
                        // echo "File Path: " . $filePath . "<br>";
                    } else {
                        $error = true;
                        $this->messageManager->addErrorMessage(__('No file uploaded.'));
                    }
                } else {
                    
                }
        

                if($error === false) {

                $insertData = [
                    'name' => $name,
                    'email' => $email,
                    'phone' => $phone,
                    'message' => $message,
                    'catalog' => $filePath,
                    'create_date' => date('Y-m-d H:i:s'),
                    'update_date' => date('Y-m-d H:i:s')
                ];

                $store = $this->storeManager->getStore();
                $templateVars = [
                    'name' => $name,
                    'email' => $email,
                    'phone' => $phone,
                    'message' => $message,
                    'catalog' => $filePath,
                    'create_date' => date('Y-m-d H:i:s'),
                    'update_date' => date('Y-m-d H:i:s')
                ];

                $postObject = new \Magento\Framework\DataObject();
                $postObject->setData($templateVars);
                $sender = [
                    'name' => "The House of Things",
                    'email' => 'info@thehouseofthings.com',
                ];

                // Insert data into the database
                $connection = $this->resource->getConnection();
                $tableName = $connection->getTableName('collaboration_lead');
                $connection->insert($tableName, $insertData);

                    $admin = "gaurav@digitalimpressions.in";

                    $transport = $this->transportBuilder
                        ->setTemplateIdentifier('collaboration_email_template')
                        ->setTemplateOptions([
                            'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                            'store' => $store->getId(),
                        ])
                        ->setTemplateVars(['data' => $postObject])
                        ->setFrom($sender, "The House of Things")
                        ->addTo($admin, "The House of Things")
                        ->getTransport()
                        ->sendMessage();


                    $this->transportBuilder
                        ->setTemplateIdentifier('collaboration_user_email_template')
                        ->setTemplateOptions([
                            'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                            'store' => $store->getId(),
                        ])
                        ->setTemplateVars(['data' => $postObject])
                        ->setFrom($sender, "The House of Things")
                        ->addTo($email, "The House of Things")
                        ->getTransport()
                        ->sendMessage();
                    $this->messageManager->addSuccessMessage(__('Thank you for your submission.'));
            }
                // Save form data to the database (implement your logic here)

                // Uncomment the following lines to send emails
                // $this->sendEmail($post, 'admin@example.com'); // Admin email
                // $this->sendEmail($post, $post['email']);       // User email
                // try {
                //     if ($error == false) {
                    

                //     //createAttachment();

                //     //$transport->sendMessage();

                //     $this->logger->info('Email sent successfully to: ' . $email);
                //     }
                // } catch (\Exception $e) {
                //     $this->logger->error('Error sending email: ' . $e->getMessage());
                // }

              
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__('Something went wrong. Please try again.'));
            }
        } else {
            $this->messageManager->addErrorMessage(__('No data received.'));
        }
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('contact-collaboration'); // Redirect to the path
        return $resultRedirect;
       // return $this->_redirect('*/*/');

        // die();
        // $this->_view->loadLayout();
        // $this->_view->getLayout()->initMessages();
        // $this->_view->renderLayout();
    }

    public function trim_input($data)
    {

        $data = trim($data);

        $data = stripslashes($data);

        $data = htmlspecialchars($data);

        return $data;
    }
}
