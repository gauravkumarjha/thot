<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/terms
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lof_ShopByBrand
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */
declare(strict_types=1);

namespace Lof\ShopByBrand\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface GroupRepositoryInterface
{

    /**
     * Save Group
     * @param \Lof\ShopByBrand\Api\Data\GroupInterface $group
     * @return \Lof\ShopByBrand\Api\Data\GroupInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Lof\ShopByBrand\Api\Data\GroupInterface $group
    );

    /**
     * Retrieve Group
     * @param string $groupId
     * @return \Lof\ShopByBrand\Api\Data\GroupInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($groupId);

    /**
     * Retrieve Group matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Lof\ShopByBrand\Api\Data\GroupSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Group
     * @param \Lof\ShopByBrand\Api\Data\GroupInterface $group
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Lof\ShopByBrand\Api\Data\GroupInterface $group
    );

    /**
     * Delete Group by ID
     * @param string $groupId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($groupId);
}
