<?php
namespace Magecomp\Gstcharge\Api;

/**
 * interface AbandonedcartInterface
 * Magecomp\Gstcharge\Api
 */
interface GstchargeInterface
{
 
    /**
     * Add gst in quote
     *
     * @param int $quoteId
     * @param int $storeId
     * @return string
     */

   public function calculateGst($quoteId,$storeId);


}