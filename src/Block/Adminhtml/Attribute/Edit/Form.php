<?php /** @noinspection PhpDeprecationInspection */

declare(strict_types=1);

namespace Infrangible\EavAttributes\Block\Adminhtml\Attribute\Edit;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Framework\Exception\LocalizedException;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2025 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Form extends Generic
{
    /**
     * @throws LocalizedException
     */
    protected function _prepareForm(): Form
    {
        $form = $this->_formFactory->create(
            ['data' => ['id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post']]
        );

        $form->setData(
            'use_container',
            true
        );

        $this->setForm($form);

        return parent::_prepareForm();
    }
}

