<?php
declare(strict_types=1);

namespace Magecomp\Gstcharge\Model\Sales\Order\Pdf\Items;

use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Filesystem;
use Magento\Framework\Filter\FilterManager;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\Stdlib\StringUtils;
use Magento\Tax\Helper\Data;

use Magento\Bundle\Model\Sales\Order\Pdf\Items\Creditmemo as CreditmemoDefualt;

class Creditmemo extends CreditmemoDefualt
{
    /**
     * Draw item line
     *
     * @return void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function draw()
    {
        $order = $this->getOrder();
        $item = $this->getItem();
        $pdf = $this->getPdf();
        $page = $this->getPage();

        $items = $this->getChildren($item);
        $prevOptionId = '';
        $drawItems = [];
        $leftBound = 35;
        $rightBound = 565;

        foreach ($items as $childItem) {
            $x = $leftBound;
            $line = [];

            $attributes = $this->getSelectionAttributes($childItem);
            if (is_array($attributes)) {
                $optionId = $attributes['option_id'];
            } else {
                $optionId = 0;
            }

            if (!isset($drawItems[$optionId])) {
                $drawItems[$optionId] = ['lines' => [], 'height' => 15];
            }

            // draw selection attributes
            if ($childItem->getOrderItem()->getParentItem() && $prevOptionId != $attributes['option_id']) {
                $line[0] = [
                    'font' => 'italic',
                    'text' => $this->string->split($attributes['option_label'], 38, true, true),
                    'feed' => $x,
                ];

                $drawItems[$optionId] = ['lines' => [$line], 'height' => 15];

                $line = [];
                $prevOptionId = $attributes['option_id'];
            }

            // draw product titles
            if ($childItem->getOrderItem()->getParentItem()) {
                $feed = $x + 5;
                $name = $this->getValueHtml($childItem);
            } else {
                $feed = $x;
                $name = $childItem->getName()."\n ,SKU :".$childItem->getSku();
            }

            $line[] = ['text' => $this->string->split($name, 35, true, true), 'feed' => $feed];

             if ($childItem->getExclPrice()) {
                $subTotal = $childItem->getrow_total();
                $itemTotal = $subTotal + $childItem->getcgst_charge() + $item->getsgst_charge() + $childItem->getigst_charge();
             } else {
                $subTotal = $childItem->getrow_total() - $childItem->getcgst_charge() - $childItem->getsgst_charge() - $childItem->getigst_charge();
             }

            $x += 65;

            // draw prices
            if ($this->canShowPriceInfo($childItem)) {
                // draw Total(ex)
                $text = $childItem->getQty()*1;
                $line[] = ['text' => $text, 'feed' => $x, 'font' => 'bold', 'align' => 'right', 'width' => 50];

                // draw Price
                $x += 45;
                $text = $order->formatPriceTxt($childItem->getPrice());
                $line[] = ['text' => $text, 'feed' => $x, 'font' => 'bold', 'align' => 'right', 'width' => 50];

                 // draw Subtotal
                $x += 50;
                $text = $order->formatPriceTxt($subTotal);
                $line[] = ['text' => $text, 'feed' => $x, 'font' => 'bold', 'align' => 'right', 'width' => 50];

                // draw Discount
                $x += 50;
                $text = $order->formatPriceTxt(-$childItem->getDiscountAmount());
                $line[] = ['text' => $text, 'feed' => $x, 'font' => 'bold', 'align' => 'right', 'width' => 50];
               
                $x += 50;
                // draw Tax
                $text = $order->formatPriceTxt($childItem->getTaxAmount());
                $line[] = ['text' => $text, 'feed' => $x, 'font' => 'bold', 'align' => 'right', 'width' => 45];

                

                $cgstamount='0.00';
                $sgstamount='0.00';
                $igstamount='0.00';
                if($childItem->getcgst_charge()){
                    $cgstamount=$childItem->getcgst_charge();
                }
                if($childItem->getsgst_charge()){
                    $sgstamount=$childItem->getsgst_charge();
                }
                 if($childItem->getigst_charge()){
                    $igstamount=$childItem->getigst_charge();
                }
                $x += 95;
                $cgst = $this->string->split($cgstamount, 10);
                $cgst[] = "(".floatval($childItem->getcgst_percent())."%)";
                $line[] = [
                    'text' => $cgst,
                    'feed' => $x,
                    'font' => 'bold',
                    'align' => 'right',
                    ];

                 $x += 65;
                $sgst = $this->string->split($sgstamount, 10);
                $sgst[] = "(".floatval($childItem->getsgst_percent())."%)";
                $line[] = [
                    'text' => $sgst,
                    'feed' => $x,
                    'font' => 'bold',
                    'align' => 'right',
                    ];

                $x += 65;
                $igst = $this->string->split($igstamount, 10);
                $igst[] = "(".floatval($childItem->getigst_percent())."%)";
                $lines[] = [
                    'text' => $igst,
                    'feed' => $x,
                    'font' => 'bold',
                    'align' => 'right',
                    ];
               
                // draw Total(inc)
                $x += 100;
                $text = $order->formatPriceTxt(
                    $childItem->getRowTotal() + $childItem->getTaxAmount() - $childItem->getDiscountAmount()
                );
                $line[] = ['text' => $text, 'feed' => $rightBound, 'font' => 'bold', 'align' => 'right'];
            }

            $drawItems[$optionId]['lines'][] = $line;
        }

        // custom options
        $options = $item->getOrderItem()->getProductOptions();
        if ($options && isset($options['options'])) {
            foreach ($options['options'] as $option) {
                $lines = [];
                $lines[][] = [
                    'text' => $this->string->split(
                        $this->filterManager->stripTags($option['label']),
                        40,
                        true,
                        true
                    ),
                    'font' => 'italic',
                    'feed' => $leftBound,
                ];

                if ($option['value']) {
                    $text = [];
                    $printValue = $option['print_value'] ?? $this->filterManager->stripTags($option['value']);
                    $values = explode(', ', $printValue);
                    foreach ($values as $value) {
                        foreach ($this->string->split($value, 30, true, true) as $subValue) {
                            $text[] = $subValue;
                        }
                    }

                    $lines[][] = ['text' => $text, 'feed' => $leftBound + 5];
                }

                $drawItems[] = ['lines' => $lines, 'height' => 15];
            }
        }

        $page = $pdf->drawLineBlocks($page, $drawItems, ['table_header' => true]);
        $this->setPage($page);
    }
}
