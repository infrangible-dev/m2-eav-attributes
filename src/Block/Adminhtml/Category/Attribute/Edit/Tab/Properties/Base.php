<?php /** @noinspection PhpDeprecationInspection */

declare(strict_types=1);

namespace Infrangible\EavAttributes\Block\Adminhtml\Category\Attribute\Edit\Tab\Properties;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2025 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Base extends \Infrangible\EavAttributes\Block\Adminhtml\Attribute\Edit\Tab\Properties\Base
{
    protected function getAdditionalFrontendInputTypes(): array
    {
        $additionalTypes = [
            ['value' => 'price', 'label' => __('Price')],
            ['value' => 'media_image', 'label' => __('Media Image')],
        ];

        $additionalReadOnlyTypes = ['gallery' => __('Gallery')];

        $attributeObject = $this->getAttributeObject();

        if (isset($additionalReadOnlyTypes[ $attributeObject->getFrontendInput() ])) {
            $additionalTypes[] = [
                'value' => $attributeObject->getFrontendInput(),
                'label' => $additionalReadOnlyTypes[ $attributeObject->getFrontendInput() ],
            ];
        }

        return $additionalTypes;
    }
}
