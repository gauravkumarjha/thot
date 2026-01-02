<?php

/**
 * Copyright © Karliuka Vitalii(karliuka.vitalii@gmail.com)
 * See COPYING.txt for license details.
 */

namespace Faonni\ShippingTweaks\Plugin\Shipping\Model\Rate;

use Magento\Quote\Model\Quote\Address\RateResult\AbstractResult;
use Magento\Shipping\Model\Rate\Result as Subject;
use Faonni\ShippingTweaks\Helper\Data as ShippingTweaksHelper;

/**
 * Shipping result plugin
 */
class Result
{
    /**
     * @var ShippingTweaksHelper
     */
    private $helper;

    /**
     * Initialize plugin
     *
     * @param ShippingTweaksHelper $helper
     */
    public function __construct(
        ShippingTweaksHelper $helper
    ) {
        $this->helper = $helper;
    }

    /**
     * Return all rates in the result
     *
     * @param Subject $subject
     * @param AbstractResult[] $result
     * @return AbstractResult[]
     */
    public function afterGetAllRates(Subject $subject, $result)
    {
        //return $this->helper->isEnabled() ? $this->updateRates($result) : $result;
        return  $this->updateRates($result);
    }

    /**
     * Retrieve updated rates in the result
     *
     * @param AbstractResult[] $result
     * @return AbstractResult[]
     */
    private function updateRates($result)
    {
        $free = [];
        $remove_free = false;
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $cart = $objectManager->get('\Magento\Checkout\Model\Cart');

        $items = $cart->getQuote()->getAllItems();
        /// print_r($items);
        if (!empty($items)) {
            $remove_free = false;
            foreach ($items as $item) {
                // echo $item->getWeight();
                if ($item->getWeight() > 0) {
                    $remove_free = true;
                }
            }
            // if( $remove_free ){
            //     foreach ($result as $shippingMethod) {
            //         echo $shippingMethod->getCarrierCode();
            //         if ( $shippingMethod->getCarrierCode() != 'freeshipping' ) {
            //             $free[] = $shippingMethod;
            //         }
            //     }  
            // }else{
            //     foreach ($result as $shippingMethod) {
            //         echo $shippingMethod->getCarrierCode();
            //         if ($shippingMethod->getCarrierCode() == 'freeshipping' ) {
            //             $free[] = $shippingMethod;
            //         }
            //     }
            // }
        }

        // if ($free) {
        //     return $free;
        // }

        // return $result;

        $freeRates = [];
        $otherRates = [];
        if (!empty($result)) {
            foreach ($result as $rate) {
                // if ( $remove_free ) {
                /* full code of shipping method */
                $code = $rate->getCarrier() . '_' . $rate->getMethod();
                // if ($this->helper->isAllFreeMethods() ||
                //     in_array($code, $this->helper->getSpecificMethods())
                // ) 
                if ($code == 'freeshipping_freeshipping') {
                    $freeRates[] = $rate;
                    // continue;
                } else {
                    $otherRates[] = $rate;
                }

                // }
            }
        }

        ///print_r( json_encode($otherRates));
        //echo "dev";
        //print_r(json_encode($freeRates));
        //echo "dev2";
        if ($remove_free) {
            return $otherRates;
        } else {
            return $freeRates;
        }
        //return $this->resolveResult($freeRates, $otherFreeRates, $result);
    }

    /**
     * Resolve result
     *
     * @param AbstractResult[] $freeRates
     * @param AbstractResult[] $otherFreeRates
     * @param AbstractResult[] $result
     * @return AbstractResult[]
     */
    // private function resolveResult(array $freeRates, array $otherFreeRates, array $result)
    // {
    //     if ( 0 < count($freeRates)) {
    //         array_push($freeRates, ...$otherFreeRates);
    //         return $freeRates;
    //     }else{

    //     }
    //     return $result;
    // }
}
