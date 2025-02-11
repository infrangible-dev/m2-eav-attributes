<?php /** @noinspection PhpDeprecationInspection */

declare(strict_types=1);

namespace Infrangible\EavAttributes\Controller\Adminhtml\Customer\Attribute;

use Infrangible\EavAttributes\Controller\Adminhtml\Customer\Attribute;
use Magento\Framework\App\Action\HttpPostActionInterface;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2025 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Delete extends Attribute implements HttpPostActionInterface
{
    public function execute()
    {
        return $this->deleteAttribute();
    }
}
