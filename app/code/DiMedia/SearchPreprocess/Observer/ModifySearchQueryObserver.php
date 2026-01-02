<?php

namespace DiMedia\SearchPreprocess\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class ModifySearchQueryObserver implements ObserverInterface
{
    public function execute(Observer $observer)
    {
        // Modify the query string directly
        $query = $observer->getEvent()->getQuery();
        $queryText = $query->getQueryText();

        // Replace "0" with "O" in the search query
        $modifiedQueryText = str_replace('0', 'O', $queryText);
        
        $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/searchgk.log');
        $logger = new \Zend_Log();
        $logger->addWriter($writer);
        $logger->info("modifiedQueryText-" . $modifiedQueryText); //to log the array
        // Set the modified query text back
        $query->setQueryText($modifiedQueryText);
    }
}
