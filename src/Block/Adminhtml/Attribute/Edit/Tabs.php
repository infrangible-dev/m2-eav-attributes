<?php

declare(strict_types=1);

namespace Infrangible\EavAttributes\Block\Adminhtml\Attribute\Edit;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2025 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    protected function _construct(): void
    {
        parent::_construct();

        $this->setData(
            'id',
            'attribute_tabs'
        );
        $this->setDestElementId('edit_form');
        $this->setData(
            'title',
            __('Attribute Information')
        );
    }

    /**
     * @throws \Exception
     */
    protected function _beforeToHtml()
    {
        $this->addTab(
            'properties',
            [
                'label'   => __('Properties'),
                'title'   => __('Properties'),
                'content' => $this->getChildHtml('properties'),
                'active'  => true
            ]
        );

        $this->addTab(
            'labels',
            [
                'label'   => __('Manage Labels'),
                'title'   => __('Manage Labels'),
                'content' => $this->getChildHtml('labels')
            ]
        );

        $this->addTab(
            'storefront',
            [
                'label'   => __('Storefront Properties'),
                'title'   => __('Storefront Properties'),
                'content' => $this->getChildHtml('storefront')
            ]
        );

        return parent::_beforeToHtml();
    }
}
