<?php
/**
 * @category Mageants GiftCard
 * @package Mageants_GiftCard
 * @copyright Copyright (c) 2016 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\GiftCard\Controller\Cart;

use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Checkout\Model\Cart as CustomerCart;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Catalog\Model\Session as CatalogSession;
use Magento\Framework\Filter\LocalizedToNormalized;
use Mageants\GiftCard\Helper\Data as GiftHelper;

/**
 * Controller for processing add to cart action.
 */
class Add extends \Magento\Checkout\Controller\Cart\Add
{
    /**
     * @var Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var Magento\Catalog\Model\Session
     */
    protected $_catalogSession;

    /**
     * @var Mageants\GiftCard\Helper\Data as GiftHelper
     */
    protected $giftHelper;

    /**
     * @param Context $context
     * @param ScopeConfigInterface $scopeConfig
     * @param CheckoutSession $checkoutSession
     * @param StoreManagerInterface $storeManager
     * @param Validator $formKeyValidator
     * @param CustomerCart $cart
     * @param ProductRepositoryInterface $productRepository
     * @param CatalogSession $catalogSession
     * @param GiftHelper $giftHelper
     */
    public function __construct(
        Context $context,
        ScopeConfigInterface $scopeConfig,
        CheckoutSession $checkoutSession,
        StoreManagerInterface $storeManager,
        Validator $formKeyValidator,
        CustomerCart $cart,
        ProductRepositoryInterface $productRepository,
        CatalogSession $catalogSession,
        GiftHelper $giftHelper
    ) {
        parent::__construct(
            $context,
            $scopeConfig,
            $checkoutSession,
            $storeManager,
            $formKeyValidator,
            $cart,
            $productRepository
        );
        $this->productRepository = $productRepository;
        $this->_catalogSession = $catalogSession;
        $this->giftHelper = $giftHelper;
    }

    /**
     * Initialize product instance from request data
     *
     * @return \Magento\Catalog\Model\Product|false
     */
    protected function _initProduct()
    {
        $productId = (int)$this->getRequest()->getParam('product');
        if ($productId) {
            $storeId = $this->_objectManager->get(
                \Magento\Store\Model\StoreManagerInterface::class
            )->getStore()->getId();
            try {
                return $this->productRepository->getById($productId, false, $storeId);
            } catch (NoSuchEntityException $e) {
                return false;
            }
        }
        return false;
    }

    /**
     * Add product to shopping cart action
     *
     * @return ResponseInterface|ResultInterface
     */
    public function execute()
    {
        if (!$this->_formKeyValidator->validate($this->getRequest())) {
            $this->messageManager->addErrorMessage(
                __('Your session has expired')
            );
            return $this->resultRedirectFactory->create()->setPath('*/*/');
        }

        $params = $this->getRequest()->getParams();
        try {
            $giftProduct = $this->_initProduct();
            if ($giftProduct->getTypeId() == "giftcertificate") {
                $giftValidate = $this->validateGiftProduct($giftProduct);
                if($giftValidate !== true) {
                    return $giftValidate;
                }
            }

            if (isset($params['qty'])) {
                $filter = new LocalizedToNormalized(
                    ['locale' => $this->_objectManager->get(
                        \Magento\Framework\Locale\ResolverInterface::class
                    )->getLocale()]
                );
                $params['qty'] = $filter->filter($params['qty']);
            }

            $product = $this->_initProduct();
            $related = $this->getRequest()->getParam('related_product');

            /** Check product availability */
            if (!$product) {
                return $this->goBack();
            }

            $this->cart->addProduct($product, $params);
            if (!empty($related)) {
                $this->cart->addProductsByIds(explode(',', $related));
            }
            $this->cart->save();

            /* custom code start */
                $this->_catalogSession->setGiftQuoteId($this->cart->getQuote()->getId());
            /* custom code end */
            
            /**
             * @todo remove wishlist observer \Magento\Wishlist\Observer\AddToCart
             */
            $this->_eventManager->dispatch(
                'checkout_cart_add_product_complete',
                ['product' => $product, 'request' => $this->getRequest(), 'response' => $this->getResponse()]
            );

            if (!$this->_checkoutSession->getNoCartRedirect(true)) {
                if ($this->shouldRedirectToCart()) {
                    $message = __(
                        'You added %1 to your shopping cart.',
                        $product->getName()
                    );
                    $this->messageManager->addSuccessMessage($message);
                } else {
                    $this->messageManager->addComplexSuccessMessage(
                        'addCartSuccessMessage',
                        [
                            'product_name' => $product->getName(),
                            'cart_url' => $this->getCartUrl(),
                        ]
                    );
                }
                if ($this->cart->getQuote()->getHasError()) {
                    $errors = $this->cart->getQuote()->getErrors();
                    foreach ($errors as $error) {
                        $this->messageManager->addErrorMessage($error->getText());
                    }
                }
                return $this->goBack(null, $product);
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            if ($this->_checkoutSession->getUseNotice(true)) {
                $this->messageManager->addNoticeMessage(
                    $this->_objectManager->get(\Magento\Framework\Escaper::class)->escapeHtml($e->getMessage())
                );
            } else {
                $messages = array_unique(explode("\n", $e->getMessage()));
                foreach ($messages as $message) {
                    $this->messageManager->addErrorMessage(
                        $this->_objectManager->get(\Magento\Framework\Escaper::class)->escapeHtml($message)
                    );
                }
            }

            $url = $this->_checkoutSession->getRedirectUrl(true);
            if (!$url) {
                $url = $this->_redirect->getRedirectUrl($this->getCartUrl());
            }

            return $this->goBack($url);
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage(
                $e,
                __('We can\'t add this item to your shopping cart right now.')
            );
            $this->_objectManager->get(\Psr\Log\LoggerInterface::class)->critical($e);
            return $this->goBack();
        }

        return $this->getResponse();
    }

    /**
     * Resolve response
     *
     * @param string $backUrl
     * @param \Magento\Catalog\Model\Product $product
     * @return ResponseInterface|ResultInterface
     */
    protected function goBack($backUrl = null, $product = null)
    {
        if (!$this->getRequest()->isAjax()) {
            return parent::_goBack($backUrl);
        }

        $result = [];

        if ($backUrl || $backUrl = $this->getBackUrl()) {
            $result['backUrl'] = $backUrl;
        } else {
            if ($product && !$product->getIsSalable()) {
                $result['product'] = [
                    'statusText' => __('Out of stock')
                ];
            }
        }

        $this->getResponse()->representJson(
            $this->_objectManager->get(\Magento\Framework\Json\Helper\Data::class)->jsonEncode($result)
        );

        return $this->getResponse();
    }

    /**
     * Returns cart url
     *
     * @return string
     */
    private function getCartUrl()
    {
        return $this->_url->getUrl('checkout/cart', ['_secure' => true]);
    }

    /**
     * Is redirect should be performed after the product was added to cart.
     *
     * @return bool
     */
    private function shouldRedirectToCart()
    {
        return $this->_scopeConfig->isSetFlag(
            'checkout/cart/redirect_to_cart',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Validates the gift product details.
     *
     * This function checks the provided gift product for required parameters
     * and ensures they meet specific criteria. It may validate properties such 
     * as gift prices, sender and recipient information, and any associated 
     * templates.
     *
     * @param array $giftProduct An associative array containing gift product data,
     *                           such as 'giftprices', 'sender-name', 'recipient-name', 
     *                           and other relevant fields.
     *
     * @return bool Returns true if the gift product is valid, false otherwise.
     */
    private function validateGiftProduct($giftProduct)
    {
        $request = $this->getRequest();
        $postData = $request->getPostValue();

        $attr = $giftProduct->getResource()->getAttribute('gifttype');
        $optionText = "";
        if ($attr->usesSource()) {
            $optionText = $attr->getSource()->getOptionText($giftProduct->getGifttype());
        }

        $requiredFields = [];

        if ($optionText == "Combined") {
            $requiredFields = [
                'giftprices',
                'gift-type',
                'sender-name',
                'sender-email'
            ];
            if (isset($postData['gift-type']) && in_array($postData['gift-type'],[0,2])) {
                $requiredFields[] = 'recipient-name';
                $requiredFields[] = 'recipient-email';
            }
        }

        if ($optionText == "Virtual") {
            $requiredFields = [
                'giftprices',
                'sender-name',
                'sender-email',
                'recipient-name',
                'recipient-email'
            ];
        }

        if ($optionText == "Printed") {
            $requiredFields = [
                'giftprices',
                'sender-name',
                'sender-email'
            ];
        }

        $dateFieldRequired = false;
        if ($this->giftHelper->isAllowDeliveryDate() && in_array($optionText,['Virtual','Printed','Combined'])) {
            $requiredFields[] = 'del-date';
            $dateFieldRequired = true;
        }

        $missingFields = [];
        $invalidEmails = [];
        $invalidDateFormat = [];

        $fieldLabels = [
            'giftprices' => __('Gift Card Value'),
            'sender-name' => __('Sender Name'),
            'sender-email' => __('Sender Email'),
            'recipient-name' => __('Recipient Name'),
            'recipient-email' => __('Recipient Email'),
            'del-date' => __('Date of certificate delivery'),
            'gift-type' => __('Gift Type'),
        ];

        foreach ($requiredFields as $field) {
            if (!isset($postData[$field]) || $postData[$field] === null || $postData[$field] === '') {
                $missingFields[] = $fieldLabels[$field] ?? $field;
            }
        }

        if (isset($requiredFields['sender-email']) && isset($postData['sender-email']) && !filter_var($postData['sender-email'], FILTER_VALIDATE_EMAIL)) {
            $invalidEmails[] = $fieldLabels['sender-email'];
        }

        if (isset($requiredFields['recipient-email']) && isset($postData['recipient-email']) && !filter_var($postData['recipient-email'], FILTER_VALIDATE_EMAIL)) {
            $invalidEmails[] = $fieldLabels['recipient-email'];
        }

        if ($dateFieldRequired && isset($postData['del-date'])) {
            $datePattern = '/^\d{4}[-\/]\d{2}[-\/]\d{2}$/';
            if (!preg_match($datePattern, $postData['del-date'])) {
                $invalidDateFormat[] = $fieldLabels['del-date'];
            }
        }

        if (!empty($missingFields)) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('The following fields are required and cannot be empty: %1', implode(', ', $missingFields))
            );
            return $this->goBack();
        }

        if (!empty($invalidEmails)) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('The following fields have invalid email formats: %1', implode(', ', $invalidEmails))
            );
            return $this->goBack();
        }

        if (!empty($invalidDateFormat)) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('The following fields have an invalid date format (required format: yyyy-mm-dd): %1', implode(', ', $invalidDateFormat))
            );
            return $this->goBack();
        }
        return true;
    }
}
