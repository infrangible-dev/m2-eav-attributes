<?php

declare(strict_types=1);

namespace Infrangible\EavAttributes\Model\Config\Source\Form;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2025 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Address implements OptionSourceInterface
{
    public function toOptionArray(): array
    {
        return [
            ['value' => 'adminhtml_customer_address', 'label' => __('Backend Customer Address')],
            ['value' => 'customer_register_address', 'label' => __('Frontend Address Create')],
            ['value' => 'customer_address_edit', 'label' => __('Frontend Address Edit')]
        ];
    }
}
