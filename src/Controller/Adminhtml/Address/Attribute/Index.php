<?php

declare(strict_types=1);

namespace Infrangible\EavAttributes\Controller\Adminhtml\Address\Attribute;

use Infrangible\EavAttributes\Controller\Adminhtml\Address\Attribute;
use Magento\Framework\App\Action\HttpGetActionInterface;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2025 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Index extends Attribute implements HttpGetActionInterface
{
    public function execute()
    {
        return $this->indexAttribute();
    }
}
