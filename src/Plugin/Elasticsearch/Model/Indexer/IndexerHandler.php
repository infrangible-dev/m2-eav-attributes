<?php

declare(strict_types=1);

namespace Infrangible\EavAttributes\Plugin\Elasticsearch\Model\Indexer;

use Infrangible\Core\Helper\EntityType;
use Infrangible\Core\Helper\Registry;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Magento\Framework\Exception\LocalizedException;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2025 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class IndexerHandler
{
    /** @var EntityType */
    protected $entityTypeHelper;

    /** @var Registry */
    protected $registryHelper;

    public function __construct(EntityType $entityTypeHelper, Registry $registryHelper)
    {
        $this->entityTypeHelper = $entityTypeHelper;
        $this->registryHelper = $registryHelper;
    }

    /**
     * @throws LocalizedException
     */
    public function aroundUpdateIndex(
        \Magento\Elasticsearch\Model\Indexer\IndexerHandler $subject,
        callable $proceed,
        array $dimensions,
        string $attributeCode
    ): \Magento\Elasticsearch\Model\Indexer\IndexerHandler {
        /** @var Attribute $attribute */
        $attribute = $this->registryHelper->registry('entity_attribute');

        if ($attribute && $attribute->getAttributeCode() === $attributeCode) {
            $productEntityType = $this->entityTypeHelper->getEntityType(Product::ENTITY);

            if ($attribute->getEntityTypeId() != $productEntityType->getId()) {
                return $subject;
            }
        }

        return $proceed(
            $dimensions,
            $attributeCode
        );
    }
}
