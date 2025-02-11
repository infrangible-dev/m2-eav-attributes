<?php /** @noinspection PhpDeprecationInspection */

declare(strict_types=1);

namespace Infrangible\EavAttributes\Block\Adminhtml\Customer\Attribute;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Helper\Data;
use Magento\Customer\Model\ResourceModel\Attribute\CollectionFactory;
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
}
