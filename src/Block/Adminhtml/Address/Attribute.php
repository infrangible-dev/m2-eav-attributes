<?php /** @noinspection PhpDeprecationInspection */

declare(strict_types=1);

namespace Infrangible\EavAttributes\Block\Adminhtml\Address;

use Magento\Backend\Block\Widget\Grid\Container;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2025 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Attribute extends Container
{
    protected function _construct()
    {
        $this->_controller = 'adminhtml_address_attribute';
        $this->_blockGroup = 'Infrangible_EavAttributes';
        $this->_headerText = __('Address Attributes');
        $this->_addButtonLabel = __('Add New Attribute');

        parent::_construct();
    }
}
