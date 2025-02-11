<?php

declare(strict_types=1);

namespace Infrangible\EavAttributes\Controller\Adminhtml\Category;

use Infrangible\Core\Helper\EntityType;
use Infrangible\Core\Helper\Registry;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\Backend\Model\View\Result\Page;
use Magento\Catalog\Helper\Product;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Product\Attribute\Frontend\Inputtype\Presentation;
use Magento\Catalog\Model\Product\Url;
use Magento\Catalog\Model\ResourceModel\Eav\AttributeFactory;
use Magento\Eav\Model\Adminhtml\System\Config\Source\Inputtype\ValidatorFactory;
use Magento\Eav\Model\Entity\Attribute\AbstractAttribute;
use Magento\Eav\Model\Validator\Attribute\Code;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Cache\FrontendInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Phrase;
use Magento\Framework\Serialize\Serializer\FormData;
use Magento\Framework\View\LayoutFactory;
use Magento\Framework\View\Result\PageFactory;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2025 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
abstract class Attribute extends \Infrangible\EavAttributes\Controller\Adminhtml\Attribute
{
    public const ADMIN_RESOURCE = 'Magento_Catalog::attributes_attributes';

    /** @var AttributeFactory */
    protected $attributeFactory;

    /** @var Presentation */
    protected $presentation;

    private const RESERVED_ATTRIBUTE_CODES = [
        'attribute_set_id',
        'parent_id',
        'path',
        'position',
        'level',
        'children_count'
    ];

    public function __construct(
        Context $context,
        FrontendInterface $attributeLabelCache,
        Registry $registryHelper,
        PageFactory $resultPageFactory,
        ForwardFactory $resultForwardFactory,
        Url $url,
        EntityType $entityTypeHelper,
        ValidatorFactory $validatorFactory,
        Product $productHelper,
        LayoutFactory $layoutFactory,
        JsonFactory $resultJsonFactory,
        AttributeFactory $attributeFactory,
        FormData $formDataSerializer = null,
        Code $attributeCodeValidator = null,
        Presentation $presentation = null,
        array $multipleAttributeList = []
    ) {
        parent::__construct(
            $context,
            $attributeLabelCache,
            $registryHelper,
            $resultPageFactory,
            $resultForwardFactory,
            $url,
            $entityTypeHelper,
            $validatorFactory,
            $productHelper,
            $layoutFactory,
            $resultJsonFactory,
            $formDataSerializer,
            $attributeCodeValidator,
            $multipleAttributeList
        );

        $this->attributeFactory = $attributeFactory;
        $this->presentation = $presentation ? : ObjectManager::getInstance()->get(Presentation::class);
    }

    /**
     * @param Phrase|string|null $title
     */
    protected function createEntityTypeActionPage($title = null): Page
    {
        return $this->createActionPage([__('Catalog'), __('Category Attributes'), $title],
            'Infrangible_EavAttributes::category_attributes');
    }

    protected function getEntityTypeId(): string
    {
        return Category::ENTITY;
    }

    protected function createModel(): \Magento\Eav\Model\Entity\Attribute
    {
        return $this->attributeFactory->create();
    }

    protected function getIndexClassName(): string
    {
        return \Infrangible\EavAttributes\Block\Adminhtml\Category\Attribute::class;
    }

    protected function getIndexPath(): string
    {
        return 'eav_attribute/category/';
    }

    protected function getReservedAttributeCodes(): array
    {
        return self::RESERVED_ATTRIBUTE_CODES;
    }

    protected function getPresentationInputType(AbstractAttribute $attribute): ?string
    {
        if ($attribute instanceof \Magento\Catalog\Model\ResourceModel\Eav\Attribute) {
            return $this->presentation->getPresentationInputType($attribute);
        } else {
            return $attribute->getFrontendInput();
        }
    }

    protected function convertPresentationDataToInputType(array $data): array
    {
        return $this->presentation->convertPresentationDataToInputType($data);
    }
}
