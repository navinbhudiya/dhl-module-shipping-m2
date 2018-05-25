<?php
/**
 * Dhl Shipping
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to
 * newer versions in the future.
 *
 * PHP version 7
 *
 * @package   Dhl\Shipping\Model
 * @author    Max Melzer <max.melzer@netresearch.de>
 * @copyright 2018 Netresearch GmbH & Co. KG
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.netresearch.de/
 */
namespace Dhl\Shipping\Model\ResourceModel\Quote;

use Dhl\Shipping\Setup\ShippingSetup;
use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;

/**
 * Resource Model for DHL Shipping Quote Address Extension
 *
 * @package  Dhl\Shipping\Model
 * @author   Max Melzer <max.melzer@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class ServiceSelection extends AbstractDb
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var ShippingInfoBuilder
     */
    private $shippingInfoBuilder;

    /**
     * QuoteAddressExtension constructor.
     * @param Context $context
     * @param EntityManager $entityManager
     * @param ShippingInfoBuilder $shippingInfoBuilder
     * @param null $connectionName
     */
    public function __construct(
        Context $context,
        EntityManager $entityManager,
        ShippingInfoBuilder $shippingInfoBuilder,
        $connectionName = null
    ) {
        $this->entityManager = $entityManager;
        $this->shippingInfoBuilder = $shippingInfoBuilder;

        parent::__construct($context, $connectionName);
    }

    /**
     * Resource initialization.
     */
    protected function _construct()
    {
        $this->_init(ShippingSetup::TABLE_SERVICE_SELECTION, 'entity_id');
    }

    /**
     * @param AbstractModel $object
     * @param mixed $value
     * @param string|null $field
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function load(AbstractModel $object, $value, $field = null)
    {
        $this->entityManager->load($object, $value);

        $info = $object->getInfo();
        if (is_string($info)) {
            $this->shippingInfoBuilder->setInfo($info);
            $info = $this->shippingInfoBuilder->create();
            $object->setData(AbstractAddressExtension::SHIPPING_INFO, $info);
        }

        return $this;
    }

    /**
     * Save object object data
     *
     * @param AbstractModel|AbstractAddressExtension $object
     * @return $this
     */
    public function save(AbstractModel $object)
    {
        $this->_isPkAutoIncrement = false;

        $shippingInfo = $object->getShippingInfo();
        if ($shippingInfo instanceof ShippingInfoInterface) {
            $object->setData(AbstractAddressExtension::INFO, json_encode($shippingInfo));
        }

        $this->entityManager->save($object);

        return $this;
    }

    /**
     * Delete the object
     *
     * @param AbstractModel|QuoteAddressExtensionInterface $object
     * @return $this
     */
    public function delete(AbstractModel $object)
    {
        $this->entityManager->delete($object);

        return $this;
    }
}
