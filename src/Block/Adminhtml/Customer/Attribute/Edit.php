<?php /** @noinspection PhpDeprecationInspection */

declare(strict_types=1);

namespace Infrangible\EavAttributes\Block\Adminhtml\Customer\Attribute;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2025 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Edit extends \Infrangible\EavAttributes\Block\Adminhtml\Attribute\Edit
{
    protected function _construct()
    {
        $this->_controller = 'adminhtml_customer_attribute';

        parent::_construct();
    }
}
