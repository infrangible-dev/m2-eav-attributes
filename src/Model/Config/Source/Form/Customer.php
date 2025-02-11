<?php

declare(strict_types=1);

namespace Infrangible\EavAttributes\Model\Config\Source\Form;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2025 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Customer implements OptionSourceInterface
{
    public function toOptionArray(): array
    {
        return [
            ['value' => 'adminhtml_checkout', 'label' => __('Backend Checkout')],
            ['value' => 'adminhtml_customer', 'label' => __('Backend Customer')],
            ['value' => 'customer_account_create', 'label' => __('Frontend Customer Create')],
            ['value' => 'customer_account_edit', 'label' => __('Frontend Customer Edit')]
        ];
    }
}
