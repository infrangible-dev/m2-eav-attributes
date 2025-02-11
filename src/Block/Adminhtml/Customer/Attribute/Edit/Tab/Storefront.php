<?php /** @noinspection PhpDeprecationInspection */

declare(strict_types=1);

namespace Infrangible\EavAttributes\Block\Adminhtml\Customer\Attribute\Edit\Tab;

use Infrangible\BackendWidget\Helper\Form;
use Infrangible\EavAttributes\Model\Config\Source\Form\Customer;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Element\Dependence;
use Magento\Eav\Block\Adminhtml\Attribute\PropertyLocker;
use Magento\Eav\Model\Entity\Attribute;
use Magento\Framework\Data\Form\Element\Fieldset;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2025 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Storefront extends \Infrangible\EavAttributes\Block\Adminhtml\Attribute\Edit\Tab\Storefront
{
    /** @var Customer */
    protected $sourceFormCustomer;

    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Form $formHelper,
        PropertyLocker $propertyLocker,
        Customer $sourceFormCustomer,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $formFactory,
            $formHelper,
            $propertyLocker,
            $data
        );

        $this->sourceFormCustomer = $sourceFormCustomer;
    }

    protected function prepareEntityFormFields(
        Fieldset $fieldset,
        Dependence $dependence,
        Attribute $attribute
    ): void {
        $this->formHelper->addSimpleIntegerField(
            $fieldset,
            'sort_order',
            __('Sort Order')->render()
        );

        $this->formHelper->addSimpleTextField(
            $fieldset,
            'multiline_count',
            __('Multiline Count')->render()
        );

        $this->formHelper->addSimpleOptionsMultiSelectField(
            $fieldset,
            'customer_forms',
            __('Customer Forms')->render(),
            $this->sourceFormCustomer->toOptionArray()
        );
    }
}
