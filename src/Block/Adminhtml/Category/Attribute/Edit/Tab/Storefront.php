<?php /** @noinspection PhpDeprecationInspection */

declare(strict_types=1);

namespace Infrangible\EavAttributes\Block\Adminhtml\Category\Attribute\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Element\Dependence;
use Magento\Eav\Model\Entity\Attribute;
use Magento\Framework\Data\Form\Element\Fieldset;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2025 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Storefront extends \Infrangible\EavAttributes\Block\Adminhtml\Attribute\Edit\Tab\Storefront
{
    protected function prepareEntityFormFields(
        Fieldset $fieldset,
        Dependence $dependence,
        Attribute $attribute
    ): void {
        $this->formHelper->addSimpleIntegerField(
            $fieldset,
            'position',
            __('Position')->render()
        );

        $this->formHelper->addSimpleYesNoField(
            $fieldset,
            'is_searchable',
            __('Use in Search')->render()
        );

        $this->formHelper->addSimpleYesNoField(
            $fieldset,
            'is_html_allowed_on_front',
            __('Allow HTML Tags on Storefront')->render()
        );

        /** @var \Magento\Catalog\Model\ResourceModel\Eav\Attribute $attribute */
        if (! $attribute->getId() || $attribute->getIsWysiwygEnabled()) {
            $attribute->setIsHtmlAllowedOnFront(1);
        }

        $this->formHelper->addSimpleYesNoField(
            $fieldset,
            'is_wysiwyg_enabled',
            __('WYSIWYG Enabled')->render()
        );

        $dependence->addFieldMap(
            'is_searchable',
            'searchable'
        );

        $dependence->addFieldMap(
            'is_html_allowed_on_front',
            'html_allowed_on_front'
        );
    }
}
