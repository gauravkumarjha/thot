<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_PaymentShippingRestriction
 * @author    Webkul
 * @copyright Copyright (c)  Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\PaymentShippingRestriction\Model;

use Magento\Framework\Model\AbstractModel;
use Webkul\PaymentShippingRestriction\Api\Data\ShippingMappingInterface;
use Magento\Framework\DataObject\IdentityInterface;

/**
 * PaymentShippingRestriction Mapping Model.
 *
 */
class ShippingMapping extends AbstractModel implements ShippingMappingInterface, IdentityInterface
{
    /**
     * DB Storage table name
     */
    const TABLE_NAME = 'payment_shipping_mapping';

    /**
     * No route page id.
     */
    const NOROUTE_ENTITY_ID = 'no-route';

    /**#@+
     * Product's Statuses
     */
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 2;
    /**#@-*/

    /**
     * PaymentShippingRestriction cache tag.
     */
    const CACHE_TAG = 'payment_shipping_mapping';

    /**
     * @var string
     */
    protected $_cacheTag = 'payment_shipping_mapping';

    /**
     * Prefix of model events names.
     *
     * @var string
     */
    protected $_eventPrefix = 'payment_shipping_mapping';

    /**
     * Initialize resource model.
     */
    protected function _construct()
    {
        $this->_init(\Webkul\PaymentShippingRestriction\Model\ResourceModel\ShippingMapping::class);
    }

    /**
     * Load object data.
     *
     * @param int|null $id
     * @param string   $field
     *
     * @return $this
     */
    public function load($id, $field = null)
    {
        if ($id === null) {
            return $this->noRouteProduct();
        }

        return parent::load($id, $field);
    }

    /**
     * Load No-Route Product.
     *
     * @return \Webkul\PaymentShippingRestriction\Model\Mapping
     */
    public function noRouteProduct()
    {
        return $this->load(self::NOROUTE_ENTITY_ID, $this->getIdFieldName());
    }

    /**
     * Get identities.
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG.'_'.$this->getId()];
    }

    /**
     * Get ID.
     *
     * @return int
     */
    public function getId()
    {
        return parent::getData(self::ENTITY_ID);
    }

    /**
     * Set ID.
     *
     * @param int $id
     *
     * @return \Webkul\PaymentShippingRestriction\Api\Data\ShippingMappingInterface
     */
    public function setId($id)
    {
        return $this->setData(self::ENTITY_ID, $id);
    }

    /**
     * Insert multiple
     *
     * @param array $data
     * @return void
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     * @throws \Exception
     */
    public function insertMultiple($data, $tableName = self::TABLE_NAME)
    {
        try {
            $tableName = $this->getResource()->getTable(self::TABLE_NAME);
            $this->connection = $this->getResource()->getConnection();
            return $this->connection->insertMultiple($tableName, $data);
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
