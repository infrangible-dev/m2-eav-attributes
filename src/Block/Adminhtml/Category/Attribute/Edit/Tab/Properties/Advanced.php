<?php /** @noinspection PhpDeprecationInspection */

declare(strict_types=1);

namespace Infrangible\EavAttributes\Block\Adminhtml\Category\Attribute\Edit\Tab\Properties;

use Magento\Eav\Model\Entity\Attribute;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Framework\Data\Form;
use Magento\Framework\Data\Form\Element\Fieldset;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2025 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Advanced extends \Infrangible\EavAttributes\Block\Adminhtml\Attribute\Edit\Tab\Properties\Advanced
{
    protected function prepareEntityFields(
        Form $form,
        Fieldset $fieldSet,
        Attribute $attribute
    ): void {
        $scopes = [
            ScopedAttributeInterface::SCOPE_STORE   => __('Store View'),
            ScopedAttributeInterface::SCOPE_WEBSITE => __('Website'),
            ScopedAttributeInterface::SCOPE_GLOBAL  => __('Global'),
        ];

        if ($attribute->getAttributeCode() == 'status' || $attribute->getAttributeCode() == 'tax_class_id') {
            unset($scopes[ ScopedAttributeInterface::SCOPE_STORE ]);
        }

        $this->formHelper->addSimpleOptionsFieldWithNote(
            $fieldSet,
            'is_global',
            __('Scope')->render(),
            $scopes,
            __('Declare attribute value saving scope.')->render()
        );
    }
}
