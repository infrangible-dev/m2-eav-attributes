<?php /** @noinspection PhpDeprecationInspection */

declare(strict_types=1);

namespace Infrangible\EavAttributes\Block\Adminhtml\Attribute;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Helper\Data;
use Magento\Eav\Block\Adminhtml\Attribute\Grid\AbstractGrid;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Collection;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2025 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
abstract class Grid extends AbstractGrid
{
    public function __construct(
        Context $context,
        Data $backendHelper,
        array $data = []
    ) {
        $this->_module = 'eav_attribute';

        parent::__construct(
            $context,
            $backendHelper,
            $data
        );
    }

    protected function _prepareCollection(): Grid
    {
        $collection = $this->createCollection();

        $collection->addFieldToFilter(
            'additional_table.is_visible',
            1
        );

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    abstract protected function createCollection(): Collection;
}
