<?php /** @noinspection PhpDeprecationInspection */

declare(strict_types=1);

namespace Infrangible\EavAttributes\Block\Adminhtml\Attribute;

use Infrangible\Core\Helper\Registry;
use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Form\Container;
use Magento\Eav\Model\Entity\Attribute;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2025 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Edit extends Container
{
    /** @var string */
    protected $_blockGroup = 'Infrangible_EavAttributes';

    /** @var Registry */
    protected $registryHelper;

    public function __construct(
        Context $context,
        Registry $registryHelper,
        array $data = []
    ) {
        $this->registryHelper = $registryHelper;

        parent::__construct(
            $context,
            $data
        );
    }

    protected function _construct()
    {
        $this->_objectId = 'attribute_id';

        parent::_construct();

        $this->buttonList->update(
            'save',
            'label',
            __('Save Attribute')
        );

        $this->buttonList->update(
            'save',
            'class',
            'save primary'
        );

        /** @noinspection PhpParamsInspection */
        $this->buttonList->update(
            'save',
            'data_attribute',
            ['mage-init' => ['button' => ['event' => 'save', 'target' => '#edit_form']]]
        );

        $this->addButton(
            'save_and_edit_button',
            [
                'label'          => __('Save and Continue Edit'),
                'class'          => 'save',
                'data_attribute' => [
                    'mage-init' => [
                        'button' => ['event' => 'saveAndContinueEdit', 'target' => '#edit_form'],
                    ],
                ]
            ]
        );

        /** @var Attribute $entityAttribute */
        $entityAttribute = $this->registryHelper->registry('entity_attribute');

        if (! $entityAttribute || ! $entityAttribute->getIsUserDefined()) {
            $this->buttonList->remove('delete');
        } else {
            $this->buttonList->update(
                'delete',
                'label',
                __('Delete Attribute')
            );
        }
    }

    public function getValidationUrl(): string
    {
        return $this->getUrl(
            '*/*/validate',
            ['_current' => true]
        );
    }

    public function getSaveUrl(): string
    {
        return $this->getUrl(
            '*/*/save',
            ['_current' => true, 'back' => null]
        );
    }
}
