<?php /** @noinspection PhpDeprecationInspection */

declare(strict_types=1);

namespace Infrangible\EavAttributes\Block\Adminhtml\Address\Attribute\Edit\Tab\Properties;

use Magento\Eav\Model\Entity\Attribute;
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
        $this->formHelper->addSimpleYesNoField(
            $fieldSet,
            'is_system',
            __('Is System')->render()
        );
    }
}
