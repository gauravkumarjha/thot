<?php

namespace FME\Quickrfq\Controller\Index;

use Magento\Framework\App\Filesystem\DirectoryList;

class Post extends \FME\Quickrfq\Controller\Index
{

    const CONFIG_CAPTCHA_ENABLE = 'quickrfq/google_options/captchastatus';
    const CONFIG_CAPTCHA_PRIVATE_KEY = 'quickrfq/google_options/googleprivatekey';

    const XML_PATH_UPLOAD_ALLOWED = 'quickrfq/upload/allow';

    private static $_siteVerifyUrl = "https://www.google.com/recaptcha/api/siteverify?";

    private static $_version = "php_1.0";

    public function execute()
    {


        $post = $this->getRequest()->getPostValue();

        $remoteAddress = new \Magento\Framework\Http\PhpEnvironment\RemoteAddress($this->getRequest());
        $visitorIp = $remoteAddress->getRemoteAddress();

        if (!$post) {
            $this->__redirect('*/*/');
            return;
        }

        /// print_r($post); die() ;     
        $this->inlineTranslation->suspend();

        $postObject = new \Magento\Framework\DataObject();
        $postObject->setData($post);

        $error = false;
        $captcha_enable = false;
        $captcha_enable = $this->scopeConfig->getValue(self::CONFIG_CAPTCHA_ENABLE);

        // if ($captcha_enable) {
        //     if (!\Zend_Validate::is(trim($post["g-recaptcha-response"]), 'NotEmpty')) {
        //         $error = true;
        //     }
        // }
        if (!\Zend_Validate::is(trim($post['company']), 'NotEmpty')) {
            $error = true;
        }
        if (!\Zend_Validate::is(trim($post['contact_name']), 'NotEmpty')) {
            $error = true;
        }
        // if (!\Zend_Validate::is(trim($post['email']), 'EmailAddress')) {
        //     $error = true;
        // }

        // if (!\Zend_Validate::is(trim($post['overview']), 'NotEmpty')) {
        //     $error = true;
        // }
        if (\Zend_Validate::is(trim($post['hideit']), 'NotEmpty')) {
            $error = true;
        }
        if ($error) {
            throw new \Exception();
        }



        /*Captcha Process*/

        // if ($captcha_enable) {
        //     $captcha =   $post["g-recaptcha-response"];
        //     $secret =  $this->scopeConfig->getValue(self::CONFIG_CAPTCHA_PRIVATE_KEY);

        //     $response = null;
        //     $path = self::$_siteVerifyUrl;
        //     $dataC =  [
        //         'secret' => $secret,
        //         'remoteip' => $visitorIp,
        //         'v' => self::$_version,
        //         'response' => $captcha
        //     ];
        //     $req = "";
        //     foreach ($dataC as $key => $value) {
        //         $req .= $key . '=' . urlencode(stripslashes($value)) . '&';
        //     }
        //     // Cut the last '&'
        //     $req = substr($req, 0, strlen($req) - 1);
        //     $response = file_get_contents($path . $req);
        //     $answers = json_decode($response, true);
        //     if (trim($answers['success']) == true) {
        //         $error = false;
        //     } else {
        //         // Dispay Captcha Error

        //         $error = true;
        //         //throw new \Exception();
        //     }
        // }

        /*Captcha Process*/

        // addCc added by chetaru
        /*Email Sending Start*/
        if ($error == false) {
            try {
                $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
                $transport = $this->_transportBuilder
                    ->setTemplateIdentifier($this->scopeConfig->getValue(self::XML_PATH_EMAIL_TEMPLATE, $storeScope))
                    ->setTemplateOptions(
                        [
                            'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                            'store' => $this->storeManager->getStore()->getId(),
                        ]
                    )
                    ->setTemplateVars(['data' => $postObject])
                    ->setFrom($this->scopeConfig->getValue(self::XML_PATH_EMAIL_SENDER, $storeScope))
                    ->addTo($this->scopeConfig->getValue(self::XML_PATH_EMAIL_RECIPIENT, $storeScope))
                    ->addTo("gaurav@digitalimpressions.in")
                    ->setReplyTo($post['email'])
                    ->getTransport();

                //createAttachment();

                $transport->sendMessage();

                //$message = $this->message;

                /*Email Sending End*/



                /* File Uploading Start */

                $post['prd'] = $this->_processFileUpload();


                /* File Uploading Ends */


                /*Save Data Start*/

                $post['create_date'] = time();
                $post['update_date'] = time();
                $model = $this->_objectManager->create('FME\Quickrfq\Model\Quickrfq');
                $model->setData($post);


                $model->save();

                /*Save Data End*/




                $this->inlineTranslation->resume();
                $this->myfreshworksUpsert();
                $this->messageManager->addSuccess(
                    __('Thanks for contacting us with your quote request. We\'ll respond to you very soon.')
                );

                $this->_redirect('quickrfq/index');
                return;
            } catch (\Exception $e) {
                //echo  $e->getMessage().'Error : We can\'t process your request right now'; exit;

                $this->inlineTranslation->resume();
                $this->messageManager->addError(
                    __($e->getMessage() . ' We can\'t process your request right now. Sorry, that\'s all we know.')
                );
                $this->_redirect('quickrfq/index');
                return;
            }
        } else {
            $this->messageManager->addError(
                __(' Invalid captcha key.')
            );
            $this->_redirect('quickrfq/index');
            return;
        }
    }



    private function _processFileUpload()
    {
        try {
            $Uploader = $this->_objectManager->create(
                'Magento\MediaStorage\Model\File\Uploader',
                ['fileId' => 'prd']
            );
        } catch (\Exception $e) {
            return false;
        }




        if ($Uploader->validateFile()['error'] > 0) {
            return false;
        }

        try {
            $result = $Uploader->validateFile();


            if (isset($result) && !empty($result['name'])) {
                $file_ext_allowed = $this->scopeConfig->getValue(self::XML_PATH_UPLOAD_ALLOWED);

                $Uploader->setAllowedExtensions(explode(',', $file_ext_allowed));
                $Uploader->setAllowCreateFolders(true);
                $Uploader->setAllowRenameFiles(true);

                $media_dir_obj = $this->_objectManager->get('Magento\Framework\Filesystem')
                    ->getDirectoryRead(DirectoryList::MEDIA);
                $media_dir = $media_dir_obj->getAbsolutePath();

                $quickrfq_dir = $media_dir . '/Quickrfq/';


                $Uploader->save($quickrfq_dir);
                return 'Quickrfq/' . $Uploader->getUploadedFileName();
            }
        } catch (\Exception $e) {
            $this->messageManager->addError(
                __($e->getMessage())
            );
        }
    }

    private function myfreshworksUpsert()
    {
        $request = $this->getRequest();
        $email = $request->getParam('email');
        $prefer_contact = $request->getParam('prefer_contact');
        $f_name = $request->getParam('company');
        $lastname = $request->getParam('contact_name');
        $country_code = $request->getParam('country_code');
        $telephone = $request->getParam('phone');
        $comment = $request->getParam('overview');
        $project_title = $request->getParam('project_title');


        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://thehouseofthings-org.myfreshworks.com/crm/sales/api/contacts/upsert',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{"unique_identifier":{"emails": "' . $email . '"}, "contact":{"first_name":"' . $f_name . '", "last_name":"' . $lastname . '","mobile_number":"' . $telephone . '"}}',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Token token=t6cbmvG6-1jolqKsJNb4eg',
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);
        if ($response !== false) {

            curl_close($curl);



            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://thehouseofthings-org.myfreshworks.com/crm/marketer/mas/api/v1/events?email=' . $email . '&event_name=quickrfq&event_timestamp=' . date("Y-m-d") . 'T20%3A00%3A00Z',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => '{
        "first_name":"' . $f_name . '", 
        "last_name":"' . $lastname . '",
        "prefer_contact":"' . $prefer_contact . '",
        "country_code":"' . $country_code . '",
        "mobile_number":"' . $telephone . '",
        "email":"' . $email . '",
        "comment":"' . $comment . '",
         "current_url":"' . $project_title . '"
        }',
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Token token=t6cbmvG6-1jolqKsJNb4eg',
                    'Content-Type: application/json'
                ),
            ));

            $response2 = curl_exec($curl);
            curl_close($curl);
            // print_r($response2);
            // die();

        }
        return $response;
    }
}
