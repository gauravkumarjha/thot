<?php
namespace DiMedia\Module\Controller\Index;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Mail\Template\TransportBuilder;

class Post extends Action
{
    protected $formKeyValidator;
    protected $transportBuilder;
    protected $messageManager;

    public function __construct(
        Context $context,
        Validator $formKeyValidator,
        TransportBuilder $transportBuilder,
        ManagerInterface $messageManager
    ) {
        $this->formKeyValidator = $formKeyValidator;
        $this->transportBuilder = $transportBuilder;
        $this->messageManager = $messageManager;
        parent::__construct($context);
    }

    public function execute()
    {
        // Validate the form key
        if (!$this->formKeyValidator->validate($this->getRequest())) {
            $this->messageManager->addErrorMessage(__('Invalid form key.'));
            return $this->_redirect('*/*/');
        }

        // Get the POST data
        $post = $this->getRequest()->getPostValue();
        if (!empty($post)) {
            // Debugging: Uncomment the following lines for development
            // print_r($post);
            // die();

            try {
                // Save form data to the database (implement your logic here)

                // Uncomment the following lines to send emails
                // $this->sendEmail($post, 'admin@example.com'); // Admin email
                // $this->sendEmail($post, $post['email']);       // User email

                $this->messageManager->addSuccessMessage(__('Thank you for your submission.'));
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__('Something went wrong. Please try again.'));
            }
        } else {
            $this->messageManager->addErrorMessage(__('No data received.'));
        }

        return $this->_redirect('*/*/');
    }   
}