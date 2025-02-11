<?php /** @noinspection PhpDeprecationInspection */

declare(strict_types=1);

namespace Infrangible\EavAttributes\Block\Adminhtml\Attribute\Edit\Tab;

use Infrangible\BackendWidget\Helper\Form;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Element\Dependence;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Catalog\Model\Entity\Attribute;
use Magento\Eav\Block\Adminhtml\Attribute\PropertyLocker;
use Magento\Framework\Data\Form\Element\Fieldset;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2025 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Storefront extends Generic
{
    /** @var Form */
    protected $formHelper;

    /** @var PropertyLocker */
    private $propertyLocker;

    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Form $formHelper,
        PropertyLocker $propertyLocker,
        array $data = []
    ) {
        $this->formHelper = $formHelper;
        $this->propertyLocker = $propertyLocker;

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
    protected function _prepareForm(): Storefront
    {
        /** @var Attribute $attribute */
        $attribute = $this->_coreRegistry->registry('entity_attribute');

        $form = $this->_formFactory->create(
            ['data' => ['id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post']]
        );

        $fieldset = $form->addFieldset(
            'storefront_fieldset',
            ['legend' => __('Storefront Properties'), 'collapsable' => false]
        );

        /** @var Dependence $dependence */
        $dependence = $this->getLayout()->createBlock(Dependence::class);

        $dependence->addFieldMap(
            'frontend_input',
            'frontend_input_type'
        );

        $this->prepareEntityFormFields(
            $fieldset,
            $dependence,
            $attribute
        );

        // define field dependencies
        $this->setChild(
            'form_after',
            $dependence
        );

        $this->setForm($form);
        $form->setValues($attribute->getData());

        $this->propertyLocker->lock($form);

        return parent::_prepareForm();
    }

    protected function prepareEntityFormFields(
        Fieldset $fieldset,
        Dependence $dependence,
        \Magento\Eav\Model\Entity\Attribute $attribute
    ): void {
    }
}
