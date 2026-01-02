<?php
namespace Mageants\GiftCard\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Checkout\Model\Session as CheckoutSession;
use Mageants\GiftCard\Model\Account;
use Mageants\GiftCard\Model\Codelist;
use Magento\Sales\Model\OrderFactory;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Directory\Model\CurrencyFactory;
use Magento\Store\Model\StoreManagerInterface;
use Mageants\GiftCard\Helper\CurrencyData;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Framework\Controller\Result\JsonFactory;
use Psr\Log\LoggerInterface;
use Mageants\GiftCard\Helper\Data;
use Magento\Directory\Model\Currency as ModelCurrency;
use Magento\Framework\App\Request\Http;

class ApplyHelper extends AbstractHelper
{

    /**
     * @var CheckoutSession
     */
    protected $checkoutSession;

    /**
     * @var Account
     */
    protected $account;

    /**
     * @var Codelist
     */
    protected $codelist;

    /**
     * @var OrderFactory
     */
    protected $orderFactory;

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrencyInterface;

    /**
     * @var CurrencyFactory
     */
    protected $currencyFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var CurrencyData
     */
    protected $currencyData;

    /**
     * @var CategoryFactory
     */
    protected $categoryFactory;

    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var Mageants\GiftCard\Helper\Data
     */
    protected $_helper;

    /**
     * @var Magento\Directory\Model\Currency
     */
    protected $_modelCurrency;

    /**
     * @var Magento\Framework\App\Request\Http
     */
    public $request;

    /**
     * @param CheckoutSession $checkoutSession
     * @param Account $account
     * @param Codelist $codelist
     * @param OrderFactory $orderFactory
     * @param PriceCurrencyInterface $priceCurrencyInterface
     * @param CurrencyFactory $currencyFactory
     * @param StoreManagerInterface $storeManager
     * @param CurrencyData $currencyData
     * @param CategoryFactory $categoryFactory
     * @param JsonFactory $resultJsonFactory
     * @param LoggerInterface $logger
     * @param Data $helper
     * @param ModelCurrency $modelCurrency
     * @param Http $request
     */
    public function __construct(
        CheckoutSession $checkoutSession,
        Account $account,
        Codelist $codelist,
        OrderFactory $orderFactory,
        PriceCurrencyInterface $priceCurrencyInterface,
        CurrencyFactory $currencyFactory,
        StoreManagerInterface $storeManager,
        CurrencyData $currencyData,
        CategoryFactory $categoryFactory,
        JsonFactory $resultJsonFactory,
        LoggerInterface $logger,
        Data $helper,
        ModelCurrency $modelCurrency,
        Http $request
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->account = $account;
        $this->codelist = $codelist;
        $this->orderFactory = $orderFactory;
        $this->priceCurrencyInterface = $priceCurrencyInterface;
        $this->currencyFactory = $currencyFactory;
        $this->storeManager = $storeManager;
        $this->currencyData = $currencyData;
        $this->categoryFactory = $categoryFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->logger = $logger;
        $this->_helper = $helper;
        $this->_modelCurrency = $modelCurrency;
        $this->request = $request;
    }

    /**
     * Apply Gift Card
     *
     * @param array $data
     * @param object $cart
     * @return void
     */
    public function applyGiftCard($data, $cart)
    {
        $resultReturn = $this->resultJsonFactory->create();
        $this->checkoutSession->unsGift();

        if (!empty($data)) {
            $catIds = $data['categoryids'];
            $quote = $cart;

            $giftCardSubtotal = 0;
            if ($quote->getItems()->count() > 1) {
                foreach ($quote->getItems()->getData() as $value) {
                    if ($value['product_type'] == 'giftcertificate') {
                        $giftCardSubtotal += $value['row_total'];
                    }
                }
            }
            $subtotal = 0;

            $totals = $cart->getQuote()->getTotals();
            $cartSubtotal = $totals['subtotal']['value'] - $giftCardSubtotal;

            $availableCode = $this->account->getCollection()
                ->addFieldToFilter('gift_code', trim($data['gift_code']))
                ->addFieldToFilter('status', 1);

            if (empty($availableCode->getData())) {
                $error = "<span style='color:#f00'>Invalid Gift Card</span>";
                $result = [0 => '1', 1 => $error];
                return $resultReturn->setData($result);
            } else {
                $catArray = [];
                foreach ($availableCode as $catList) {
                    $catArray = explode(",", $catList->getCategories());
                }
                foreach ($availableCode->getData() as $code) {
                    $orderIncrementId = $code['order_increment_id'];
                    $order = $this->orderFactory->create()->loadByIncrementId($orderIncrementId);
                    if ($order->getStatus() == "canceled" || $order->getStatus() == "closed") {
                        $error = "<span style='color:#f00'>Invalid Gift Card Code</span>";
                        $result = [0 => '5', 1 => $error];
                        return $resultReturn->setData($result);
                    }
                }

                return $this->getCart(
                    $catArray,
                    $catIds,
                    $availableCode,
                    $subtotal,
                    $giftCardSubtotal,
                    $cart,
                    $resultReturn
                );
            }
        }
    }

    /**
     * Return to product detail in cart
     *
     * @param array $catArray
     * @param string $catIds
     * @param object $availableCode
     * @param int $subtotal
     * @param int $gift_card_subtotal
     * @param int $cart
     * @param object $resultReturn
     */
    protected function getCart($catArray, $catIds, $availableCode, $subtotal, $gift_card_subtotal, $cart, $resultReturn)
    {
        if (!empty($catArray)) {
            foreach ($catIds as $value) {
                $resultArray = explode(',', trim($value));
            }
            foreach ($catIds as $cat) {
                $category = $this->categoryFactory->create()->load(trim($cat));
            }

            $category = $category->getParentIds();
            $resultArray = array_merge($resultArray, $category);
            $data = array_intersect($resultArray, $catArray);

            if (!$data) {
                $catArrayString = implode(" ", $catArray);
                foreach ($catIds as $catId) {
                    $catIdsNew = str_replace(' ', '', $catId);
                    $catIdsArray = explode(",", $catIdsNew);

                    if (in_array($catArrayString, $catIdsArray)) {
                        $this->cartDetail($catIds, $catArray, $subtotal, $resultReturn);
                    } else {
                        $error = "<span style='color:#f00'>Sorry, Gift Card not available for this category/Categories</span>";
                        $result = [0 => '5', 1 => $error];
                        return $resultReturn->setData($result);
                    }
                }
            } else {
                $this->cartDetail($catIds, $catArray, $subtotal, $resultReturn);
            }
        }
        $certificate_value = 0;

        /* For Cart Price Rule Discount */
        $allCartItems = $cart->getQuote()->getAllVisibleItems();
        $totalDiscount = 0;
        foreach ($allCartItems as $item) {
            $totalDiscount += $item->getDiscountAmount();
        }

        foreach ($availableCode as $code) {
            if ($code->getCurrentBalance() == 0):
                $error = "<span style='color:black'>You Don't have enough balance.</span>";
                $result = [
                 0 => '2',
                 1 => $error
                ];
                return $resultReturn->setData($result);
            endif;

            if (!$this->_helper->allowSelfUse()):

                if ($code->getCustomerId() == $this->_helper->getCustomerId()):
                    $error = "<span style='color:#f00'>Sorry, You cannot use certificate for yourself</span>";
                    $result = [
                     0 => '4',
                     1 => $error
                    ];
                    return $resultReturn->setData($result);
                endif;
            endif;

            if ($code->getExpireAt() != '0000-00-00' && $code->getExpireAt() != '1970-01-01'):
                $currentDate = date('Y-m-d');
                if ($currentDate > $code->getExpireAt()):
                    $error = "<span style='color:#f00'>Sorry, This Gift Card Has Been Expired</span>";
                    $result = [
                     0 => '4',
                     1 => $error
                    ];
                    return $resultReturn->setData($result);
                endif;
            endif;

            $current_currency = $this->_helper->getCurrency();
            $getCurrency = $this->storeManager->getStore()->getCurrentCurrencyCode();

            $certificate_value = $code->getAllowBalance();

            $currencyCodeTo = $this->currencyData->getCurrentCurrency();
            $currencyCodeFrom = $this->currencyData->getBaseCurrency();
            $rate = $this->currencyFactory->create()->load($currencyCodeFrom)->getAnyRate($currencyCodeTo);

            $certificate_value *= $rate;

            $currentbal_price_with_symbol = $this->priceCurrencyInterface->convertAndFormat(
                $code->getCurrentBalance(),
                false,
                2,
                null,
                $current_currency
            );

            $currentbal = substr($this->_modelCurrency->format(
                $currentbal_price_with_symbol,
                ['precision'=>2],
                false
            ), 1);

            $percentbal_price_with_symbol = $this->priceCurrencyInterface->convertAndFormat(
                $code->getPercentage(),
                false,
                2,
                null,
                $current_currency
            );
            $percentbal = substr($this->_modelCurrency
                                ->format(
                                    $percentbal_price_with_symbol,
                                    [],
                                    false
                                ), 1);

            if ($certificate_value == '' && $percentbal == '') {
                $error = "<span style='color:#f00'>Please wait for admin approval</span>";

                $result = [
                 0 => '4',
                 1 => $error
                ];

                return $resultReturn->setData($result);
            }
            $currentbal = str_replace(',', '', $currentbal);
            if ($currentbal < $certificate_value) {
                $certificate_value = $currentbal;
            }
            $quote = $cart;
            $totals = $cart->getQuote()
             ->getTotals();

            $cart_subtotal = $totals['subtotal']['value'] - $gift_card_subtotal;
            ($totalDiscount > 0) ? $cart_subtotal -= $totalDiscount : '';
            $gift_value = $cart_subtotal;

            if ($certificate_value < $cart_subtotal) {
                $gift_value = $certificate_value;
            }

            $action = $this->request->getFullActionName();

            $accund_id = $code->getAccountId();
            $updateblance = '';
            if ($gift_value) {
                $currencyCodeTo = $this->currencyData->getCurrentCurrency();
                $currencyCodeFrom = $this->currencyData->getBaseCurrency();
                $rate = $this->currencyFactory->create()->load($currencyCodeTo)->getAnyRate($currencyCodeFrom);

                $result = str_replace(',', '', $gift_value);
                $convert_gift_value = $result * $rate;

                $this->checkoutSession->setGifts($convert_gift_value);
                
                if ($code->getCurrentBalance() < $convert_gift_value) {
                    $updateblance = $code->getCurrentBalance() - (int)$convert_gift_value;
                } else {
                    $updateblance = $code->getCurrentBalance() - $convert_gift_value;
                }
            }

            if ($code->getDiscountType() == "percent") {
                $certificate_value = $code->getPercentage();
                if ($certificate_value > 100) {
                    $discount = 1;
                } else {
                    $discount = $certificate_value;
                }

                $gift_value = ($code['initial_code_value'] * $discount) / 100;

                if ($gift_value > (int)$cart_subtotal) {
                    $gift_value = (int)$cart_subtotal;
                } elseif ($gift_value > $code->getCurrentBalance()) {
                    $gift_value = $code->getCurrentBalance();
                }
                $updateblance = $code->getCurrentBalance() - $gift_value;
            }

            $result = [
             0 => '3',
             1 => 'Gift Card Accepted'
            ];

            $resultReturn->setData($result);

            $this
             ->checkoutSession
             ->setGift($gift_value);

            $this
             ->checkoutSession
             ->setGiftCardCode($code->getGiftCode());

            $this
             ->checkoutSession
             ->setAccountid($accund_id);

            $this
             ->checkoutSession
             ->setGiftbalance($updateblance);

            $this
             ->checkoutSession
             ->getQuote()
             ->collectTotals()
             ->save();
            $cartQuote = $quote->getQuote();
            $cartQuote->getShippingAddress()
             ->setCollectShippingRates(true);
            return $resultReturn;

        }
    }

    /**
     * Return to product detail in cart
     *
     * @param string $catids
     * @param array $cat_array
     * @param int $subtotal
     * @param object $resultReturn
     */
    public function cartDetail($catids, $cat_array, $subtotal, $resultReturn)
    {
        $key_val = 0;
        $check_flag = false;
        foreach ($catids as $catid) {
            $id = explode(",", $catid);
            $size = count($id);

            foreach ($id as $i) {
                foreach ($cat_array as $cat) {
                    if ($cat == $i) {
                        $check_flag = true;
                        $key_val = 1;
                    }
                }
            }
            if ($check_flag == true) {
                $subtotal += (int)$id[$size - 1];
                $check_flag = false;
            }
        }
        if ($key_val == 0):
            if (empty($key) || $key == 0):
                $error = "<span style='color:#f00'>Sorry,
                Gift Card not available for this category/Categories</span>";

                $result = [
                0 => '5',
                1 => $error
                ];
                return $resultReturn->setData($result);
            endif;
        endif;
    }

    public function removeGiftCard()
    {
        $this->checkoutSession->setGiftCardCode("");
        $this->checkoutSession->unsGift();
    }
}
