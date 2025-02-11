<?php /** @noinspection PhpDeprecationInspection */

declare(strict_types=1);

namespace Infrangible\EavAttributes\Block\Adminhtml\Category\Attribute;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Helper\Data;
use Magento\Catalog\Model\ResourceModel\Category\Attribute\CollectionFactory;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Collection;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2025 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Grid extends \Infrangible\EavAttributes\Block\Adminhtml\Attribute\Grid
{
    /** @var CollectionFactory */
    protected $collectionFactory;

    public function __construct(
        Context $context,
        Data $backendHelper,
        CollectionFactory $collectionFactory,
        array $data = []
    ) {
        $this->collectionFactory = $collectionFactory;

        parent::__construct(
            $context,
            $backendHelper,
            $data
        );
    }

    protected function createCollection(): Collection
    {
        return $this->collectionFactory->create();
    }

    /**
     * @throws \Exception
     */
    protected function _prepareColumns(): \Infrangible\EavAttributes\Block\Adminhtml\Attribute\Grid
    {
        parent::_prepareColumns();

        $this->addColumnAfter(
            'is_global',
            [
                'header'   => __('Scope'),
                'sortable' => true,
                'index'    => 'is_global',
                'type'     => 'options',
                'options'  => [
                    ScopedAttributeInterface::SCOPE_STORE   => __('Store View'),
                    ScopedAttributeInterface::SCOPE_WEBSITE => __('Web Site'),
                    ScopedAttributeInterface::SCOPE_GLOBAL  => __('Global'),
                ],
                'align'    => 'center'
            ],
            'is_visible'
        );

        $this->addColumn(
            'is_searchable',
            [
                'header'   => __('Searchable'),
                'sortable' => true,
                'index'    => 'is_searchable',
                'type'     => 'options',
                'options'  => ['1' => __('Yes'), '0' => __('No')],
                'align'    => 'center'
            ]
        );

        return $this;
    }
}
