<?php

namespace Dimedia\Utility\Controller\Checkout;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\Action\HttpGetActionInterface;

class Items implements HttpGetActionInterface, HttpPostActionInterface
{
    protected $pageFactory;

    protected $abandonedFactory;

    public function __construct(
        Context $context,
        PageFactory $pageFactory,
        \Dimedia\Utility\Model\AbandonedFactory $abandonedFactory
    ) {
        $this->pageFactory = $pageFactory;
        $this->abandonedFactory = $abandonedFactory;
    }

    public function execute()
    {
        try {

            $skusSelected = array();

            $abandonedObject = $this->abandonedFactory->create();
            $abandonedCollection = $abandonedObject->getCollection();
            $listHtml = '<!DOCTYPE html>
                <html>
                    <head>
                        <meta charset="utf-8">
                        <meta http-equiv="X-UA-Compatible" content="IE=edge">
                        <title></title>
                    </head>
                    <body>
                    <table style="border: 1px solid;"><tr><th>S.No</th><th>Email</th><th>Customer Name</th><th>Telephone</th><th>Skus</th><th style="width:150px;">Date</th></tr>
                    ';
            $sno = 1;
            $skuString = "";
            foreach ($abandonedCollection as $_abandonedItem) {
                $skus = @unserialize($_abandonedItem->getSkus());
                if ($skus !== false) {
                    foreach ($skus as $sku) {
                        $skusSelected[] = $sku['sku'];
                    }

                    if (isset($skusSelected)) {
                        $skuString = implode(', ', $skusSelected);
                    }

                    $listHtml .= '<tr><td>' . $sno . '</td><td>' . $_abandonedItem->getCustomerEmail() . '</td><td>' . $_abandonedItem->getCustomerName() . '</td><td>' . $_abandonedItem->getCustomerTelephone() . '</td><td>' . $skuString . '</td><td>' . date("jS M Y", strtotime($_abandonedItem->getCreatedAt())) . '</td></tr>';
                    $sno++;
                    unset($skusSelected);
                    $skuString = "";
                }

                //break;
            }

            $listHtml .= '</table>
                </body>
            </html>';

            echo $listHtml;

            echo '<style>
                table {
                    display: table;
                    border-collapse: collapse;
                    width: 80%;
                    box-sizing: border-box;
                    text-indent: initial;
                    border-spacing: 2px;
                    border-color: grey;
            } 
            table, td, th {
                border: 1px solid #ddd;
                text-align: left;
                padding: 5px;
            }

            html, body {
                font-family: Verdana,sans-serif;
                font-size: 15px;
                line-height: 1.5em;
            }
            </style>';
        } catch (Exception $e) {
            echo $e->getMessage();
        }

        return $this->pageFactory->create();
    }
}
