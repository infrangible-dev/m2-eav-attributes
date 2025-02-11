<?php /** @noinspection PhpDeprecationInspection */

declare(strict_types=1);

namespace Infrangible\EavAttributes\Controller\Adminhtml\Address\Attribute;

use Infrangible\EavAttributes\Controller\Adminhtml\Address\Attribute;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Validator\ValidateException;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2025 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Validate extends Attribute implements HttpGetActionInterface, HttpPostActionInterface
{
    /**
     * @throws LocalizedException
     * @throws ValidateException
     */
    public function execute()
    {
        return $this->validateAttribute();
    }
}
