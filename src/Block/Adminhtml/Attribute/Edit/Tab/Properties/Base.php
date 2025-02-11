<?php /** @noinspection PhpDeprecationInspection */

declare(strict_types=1);

namespace Infrangible\EavAttributes\Block\Adminhtml\Attribute\Edit\Tab\Properties;

use Magento\Eav\Block\Adminhtml\Attribute\Edit\Main\AbstractMain;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Data\Form\Element\Fieldset;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2025 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Base extends AbstractMain
{
    protected function _prepareForm(): AbstractMain
    {
        parent::_prepareForm();

        $this->removeUnusedFields();
        $this->processFrontendInputTypes();

        return $this;
    }

    protected function processFrontendInputTypes(): void
    {
        $form = $this->getForm();

        /** @var AbstractElement $frontendInput */
        $frontendInput = $form->getElement('frontend_input');

        $additionalTypes = $this->getAdditionalFrontendInputTypes();

        $frontendInputValues = array_merge(
            $frontendInput->getDataUsingMethod('values'),
            $additionalTypes
        );

        $frontendInput->setDataUsingMethod(
            'values',
            $frontendInputValues
        );
    }

    protected function getAdditionalFrontendInputTypes(): array
    {
        return [];
    }

    protected function removeUnusedFields(): void
    {
        $form = $this->getForm();

        /* @var Fieldset $fieldset */
        $fieldset = $form->getElement('base_fieldset');

        $fieldsToRemove = ['attribute_code', 'is_unique', 'frontend_class'];

        foreach ($fieldset->getElements() as $element) {
            /** @var AbstractElement $element */
            if ($element->getId() && substr(
                    $element->getId(),
                    0,
                    strlen('default_value')
                ) === 'default_value') {
                $fieldsToRemove[] = $element->getId();
            }
        }

        foreach ($fieldsToRemove as $id) {
            $fieldset->removeField($id);
        }
    }
}
