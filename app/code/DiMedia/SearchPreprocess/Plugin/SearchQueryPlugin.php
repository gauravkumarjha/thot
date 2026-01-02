<?php

namespace DiMedia\SearchPreprocess\Plugin;

use Magento\Search\Model\Query;

class SearchQueryPlugin
{
    /**
     * Modify the search query before processing.
     *
     * @param Query $subject
     * @param string $queryText
     * @return string
     */
    public function beforeSetQueryText(Query $subject, $queryText)
    {

        // Replace any instance of '0' with 'O' in the search query
        $modifiedQueryText = str_replace('of', 'Of', $queryText);

        $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/search.log');
        $logger = new \Zend_Log();
        $logger->addWriter($writer);
        $logger->info("modifiedQueryText-" . $modifiedQueryText); //to log the array

        // Return the modified query text
        return [$modifiedQueryText];
    }
}
