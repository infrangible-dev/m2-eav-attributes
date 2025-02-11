<?php /** @noinspection PhpDeprecationInspection */

declare(strict_types=1);

namespace Infrangible\EavAttributes\Block\Adminhtml\Attribute\Edit\Tab\Properties;

use Infrangible\BackendWidget\Helper\Form;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Eav\Block\Adminhtml\Attribute\PropertyLocker;
use Magento\Eav\Helper\Data;
use Magento\Eav\Model\Entity\Attribute;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Data\Form\Element\Fieldset;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2025 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Advanced extends Generic
{
    /** @var Form */
    protected $formHelper;

    /** @var Data */
    protected $eavHelper;

    /** @var PropertyLocker */
    private $propertyLocker;

    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Form $formHelper,
        Data $eavHelper,
        array $data = []
    ) {
        $this->formHelper = $formHelper;
        $this->eavHelper = $eavHelper;

        parent::__construct(
            $context,
            $registry,
            $formFactory,
            $data
        );
    }

    /**
     * @throws LocalizedException
     */
    protected function _prepareForm(): Advanced
    {
        $attribute = $this->getAttribute();

        $form = $this->formHelper->createSimpleForm(
            'edit_form',
            $this->getData('action')
        );

        $fieldSet = $form->addFieldset(
            'advanced_fieldset',
            ['legend' => __('Advanced Attribute Properties'), 'collapsable' => true]
        );

        $this->formHelper->addSimpleTextFieldWithNoteAndClass(
            $fieldSet,
            'attribute_code',
            __('Attribute Code')->render(),
            __(
                'This is used internally. Make sure you don\'t use spaces or more than %1 symbols.',
                Attribute::ATTRIBUTE_CODE_MAX_LENGTH
            )->render(),
            sprintf(
                'validate-code validate-length maximum-length-%d',
                Attribute::ATTRIBUTE_CODE_MAX_LENGTH
            )
        );

        $this->formHelper->addSimpleTextFieldWithValue(
            $fieldSet,
            'default_value_text',
            __('Default Value')->render(),
            $attribute->getDefaultValue()
        );

        $this->formHelper->addSimpleYesNoField(
            $fieldSet,
            'default_value_yesno',
            __('Default Value')->render()
        );

        $this->formHelper->addSimpleDateFieldWithValue(
            $fieldSet,
            'default_value_date',
            __('Default Value')->render(),
            $attribute->getDefaultValue()
        );

        $this->formHelper->addSimpleDateTimeFieldWithValue(
            $fieldSet,
            'default_value_datetime',
            __('Default Value')->render(),
            $this->getLocalizedDateDefaultValue()
        );

        $this->formHelper->addSimpleTextareaFieldWithValue(
            $fieldSet,
            'default_value_textarea',
            __('Default Value')->render(),
            $attribute->getDefaultValue()
        );

        $this->formHelper->addSimpleYesNoField(
            $fieldSet,
            'is_unique',
            __('Unique Value')->render()
        );

        $this->formHelper->addSimpleOptionsField(
            $fieldSet,
            'frontend_class',
            __('Input Validation for Store Owner')->render(),
            $this->eavHelper->getFrontendClasses($attribute->getEntityType()->getEntityTypeCode())
        );

        if ($attribute->getId()) {
            $attributeCodeElement = $form->getElement('attribute_code');
            $attributeCodeElement->setData(
                'disabled',
                1
            );

            if (! $attribute->getIsUserDefined()) {
                $isUniqueElement = $form->getElement('is_unique');
                $isUniqueElement->setData(
                    'disabled',
                    1
                );
            }
        }

        $this->prepareEntityFields(
            $form,
            $fieldSet,
            $attribute
        );

        $this->setForm($form);
        $this->getPropertyLocker()->lock($form);

        return $this;
    }

    protected function _initFormValues(): Advanced
    {
        $this->getForm()->addValues($this->getAttribute()->getData());

        return parent::_initFormValues();
    }

    private function getAttribute(): Attribute
    {
        return $this->_coreRegistry->registry('entity_attribute');
    }

    private function getPropertyLocker()
    {
        if (null === $this->propertyLocker) {
            $this->propertyLocker = ObjectManager::getInstance()->get(PropertyLocker::class);
        }

        return $this->propertyLocker;
    }

    /**
     * @throws LocalizedException
     */
    private function getLocalizedDateDefaultValue(): string
    {
        $attributeObject = $this->getAttribute();

        if (empty($attributeObject->getDefaultValue()) || $attributeObject->getFrontendInput() !== 'datetime') {
            return (string)$attributeObject->getDefaultValue();
        }

        try {
            $localizedDate = $this->_localeDate->date(
                $attributeObject->getDefaultValue(),
                null,
                false
            );
            $localizedDate->setTimezone(new \DateTimeZone($this->_localeDate->getConfigTimezone()));
            $localizedDate = $localizedDate->format(DateTime::DATETIME_PHP_FORMAT);
        } catch (\Exception $e) {
            throw new LocalizedException(__('The default date is invalid.'));
        }

        return $localizedDate;
    }

    protected function prepareEntityFields(
        \Magento\Framework\Data\Form $form,
        Fieldset $fieldSet,
        Attribute $attribute
    ): void {
    }
}
