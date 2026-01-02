<?php
namespace Magecomp\Gstcharge\Model\Sales\Order\Pdf\Items;

use Magento\Bundle\Model\Sales\Order\Pdf\Items\Invoice as InvoiceDefualt;

class Invoice extends InvoiceDefualt
{
    
    public function draw()
    {
 		$draw = $this->drawChildrenItems();
        $draw = $this->drawCustomOptions($draw);

        $page = $this->getPdf()->drawLineBlocks($this->getPage(), $draw, ['table_header' => true]);

        $this->setPage($page);
       
    }
    /**
     * Draw bundle product children items
     *
     * @return array
     */
    private function drawChildrenItems(): array
    {

        $this->_setFontRegular();
        $prevOptionId = '';
        $drawItems = [];
        $optionId = 0;
        $lines = [];
        foreach ($this->getChildren($this->getItem()) as $childItem) {
            $index = array_key_last($lines) !== null ? array_key_last($lines) + 1 : 0;

            $attributes = $this->getSelectionAttributes($childItem);
            if (is_array($attributes)) {
                $optionId = $attributes['option_id'];
            }

            if (!isset($drawItems[$optionId])) {
                $drawItems[$optionId] = ['lines' => [], 'height' => 15];
            }

            if ($childItem->getOrderItem()->getParentItem() && $prevOptionId != $attributes['option_id']) {
                $lines[$index][] = [
                    'font' => 'italic',
                    'text' => $this->string->split($attributes['option_label'], 35, true, true),
                    'feed' => 35,
                ];

                $index++;
                $prevOptionId = $attributes['option_id'];
            }

            /* in case Product name is longer than 80 chars - it is written in a few lines */
            if ($childItem->getOrderItem()->getParentItem()) {
                $feed = 30;
                $name = $this->getValueHtml($childItem)."\n SKU :".$childItem->getSku();

            } else {
                $feed = 25;
                $name = $childItem->getName();

            }

		$taxableAmount=$childItem->getrow_total() - $childItem->getdiscount_amount();
        if ($childItem->getExclPrice()) {
            $subTotal = $childItem->getrow_total();
            $itemTotal = $subTotal + $childItem->getcgst_charge() + $childItem->getsgst_charge() + $childItem->getigst_charge();
        } else {
            $subTotal = $childItem->getrow_total() - $childItem->getcgst_charge() - $childItem->getsgst_charge() - $childItem->getigst_charge();
            $taxableAmount=$subTotal - $childItem->getdiscount_amount();
            $itemTotal = $taxableAmount + $childItem->getcgst_charge() + $childItem->getsgst_charge() + $childItem->getigst_charge();
        }


        
            $lines[$index][] = ['text' => $this->string->split($name, 15, true, true), 'feed' => $feed];

        $index = array_key_last($lines);

        	/* drawPrices start */
	        if ($this->canShowPriceInfo($childItem)) {
	        
	            $tax = $this->getOrder()->formatPriceTxt($childItem->getTaxAmount());
	        
	            $item = $this->getItem();
	            $this->_item = $childItem;
	            $feedPrice = 140;
	            $feedSubtotal = $feedPrice + 185;
	            foreach ($this->getItemPricesForDisplay() as $priceData) {
	                if (isset($priceData['label'])) {
	                    // draw Price label
	                    $lines[$index][] = ['text' => $priceData['label'], 'feed' => $feedPrice, 'align' => 'right'];
	                    // draw Subtotal label
	                    $lines[$index][] = ['text' => $priceData['label'], 'feed' => $feedSubtotal, 'align' => 'right'];
	                    $index++;
	                }
	                $lines[$index][] = [
	                    'text' => round($childItem->getQty(), 2),
	                    'feed' => $feedPrice,
	                    'font' => 'bold',
	                    'align' => 'right',
	                ];

	                // draw Price
	                $lines[$index][] = [
	                    'text' => $priceData['price'],
	                    'feed' => $feedPrice+40,
	                    'font' => 'bold',
	                    'align' => 'right',
	                ];

	                // draw Subtotal
	                $lines[$index][] = [
	                    'text' => $priceData['subtotal'],
	                    'feed' => $feedPrice+90,
	                    'font' => 'bold',
	                    'align' => 'right',
	                ];

	                $lines[$index][] = [
	                    'text' => $childItem->getDiscountAmount()?round($childItem->getDiscountAmount(), 2):'0.00',
	                    'feed' => $feedPrice+130,
	                    'font' => 'bold',
	                    'align' => 'right',
	                ];

	                $lines[$index][] = [
	                    'text' => $tax,
	                    'feed' => $feedPrice+200,
	                    'font' => 'bold',
	                    'align' => 'right',
	                ];

	                $cgst = $this->string->split($childItem->getcgst_charge(), 10);
	                $cgst[] = "(".floatval($childItem->getcgst_percent())."%)";
	                $lines[$index][] = [
	                'text' => $cgst,
	                'feed' => $feedPrice+260,
	                'font' => 'bold',
	                'align' => 'right',
	                ];

	                $sgst = $this->string->split($childItem->getsgst_charge(), 10);
	                $sgst[] = "(".floatval($childItem->getsgst_percent())."%)";
	                $lines[$index][] = [
	                'text' => $sgst,
	                'feed' => $feedPrice+310,
	                'font' => 'bold',
	                'align' => 'right',
	                ];

	                $igst = $this->string->split($childItem->getigst_charge(), 10);
	                $igst[] = "(".floatval($childItem->getigst_percent())."%)";
	                $lines[$index][] = [
	                'text' => $igst,
	                'feed' => $feedPrice+360,
	                'font' => 'bold',
	                'align' => 'right',
	                ];


	                $lines[$index][] = [
	                    'text' => $this->getOrder()->formatPriceTxt($childItem->getRowTotal()+$childItem->getTaxAmount()),
	                    'feed' => $feedSubtotal+230,
	                    'font' => 'bold',
	                    'align' => 'right',
	                ];
	                $index++;
	            }
	            $this->_item = $item;
	        }
	        /* drawPrices End */


        }
        $drawItems[$optionId]['lines'] = $lines;

        return $drawItems;
    }

    /**
     * Draw bundle product custom options
     *
     * @param array $draw
     * @return array
     */
    private function drawCustomOptions(array $draw): array
    {
        $options = $this->getItem()->getOrderItem()->getProductOptions();
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
                    'feed' => 35,
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

                    $lines[][] = ['text' => $text, 'feed' => 40];
                }

                $draw[] = ['lines' => $lines, 'height' => 15];
            }
        }

        return $draw;
    }
   
}

