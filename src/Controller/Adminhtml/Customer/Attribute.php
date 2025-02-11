<?php

declare(strict_types=1);

namespace Infrangible\EavAttributes\Controller\Adminhtml\Customer;

use FeWeDev\Base\Variables;
use Infrangible\Core\Helper\Database;
use Infrangible\Core\Helper\EntityType;
use Infrangible\Core\Helper\Registry;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\Backend\Model\View\Result\Page;
use Magento\Catalog\Helper\Product;
use Magento\Catalog\Model\Product\Url;
use Magento\Customer\Model\AttributeFactory;
use Magento\Customer\Model\Customer;
use Magento\Eav\Model\Adminhtml\System\Config\Source\Inputtype\ValidatorFactory;
use Magento\Eav\Model\Entity;
use Magento\Eav\Model\Validator\Attribute\Code;
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

    /** @var Database */
    protected $databaseHelper;

    /** @var EntityType */
    protected $entityTypeHelper;

    /** @var \Infrangible\Core\Helper\Attribute */
    protected $attributeHelper;

    /** @var Variables */
    protected $variables;

    private const RESERVED_ATTRIBUTE_CODES = [
        'website_id',
        'email',
        'group_id',
        'increment_id',
        'store_id',
        'created_at',
        'updated_at',
        'is_active',
        'disable_auto_group_change',
        'created_in',
        'prefix',
        'firstname',
        'middlename',
        'lastname',
        'suffix',
        'dob',
        'password_hash',
        'rp_token',
        'rp_token_created_at',
        'default_billing',
        'default_shipping',
        'taxvat',
        'confirmation',
        'gender',
        'failures_num',
        'first_failure',
        'lock_expires',
        'session_cutoff'
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
        Database $databaseHelper,
        \Infrangible\Core\Helper\Attribute $attributeHelper,
        Variables $variables,
        FormData $formDataSerializer = null,
        Code $attributeCodeValidator = null,
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
        $this->databaseHelper = $databaseHelper;
        $this->attributeHelper = $attributeHelper;
        $this->entityTypeHelper = $entityTypeHelper;
        $this->variables = $variables;
    }

    /**
     * @param Phrase|string|null $title
     */
    protected function createEntityTypeActionPage($title = null): Page
    {
        return $this->createActionPage([__('Customer'), __('Customer Attributes'), $title],
            'Infrangible_EavAttributes::customer_attributes');
    }

    protected function getEntityTypeId(): string
    {
        return Customer::ENTITY;
    }

    protected function createModel(): Entity\Attribute
    {
        return $this->attributeFactory->create();
    }

    protected function getIndexClassName(): string
    {
        return \Infrangible\EavAttributes\Block\Adminhtml\Customer\Attribute::class;
    }

    protected function getIndexPath(): string
    {
        return 'eav_attribute/category/';
    }

    protected function getReservedAttributeCodes(): array
    {
        return self::RESERVED_ATTRIBUTE_CODES;
    }

    protected function addEntityAttributeData(Entity\Attribute $attribute): void
    {
        $formSelect = $this->databaseHelper->select(
            'customer_form_attribute',
            ['form_code']
        );

        $formSelect->where(
            'attribute_id = ?',
            $attribute->getId()
        );

        $customerFormCodes = $this->databaseHelper->fetchCol($formSelect);

        if ($customerFormCodes) {
            $attribute->setData(
                'customer_forms',
                $customerFormCodes
            );
        }
    }

    /**
     * @throws \Exception
     */
    protected function saveEntityAttributeData(Entity\Attribute $attribute): void
    {
        $dbAdapter = $this->databaseHelper->getDefaultConnection();

        $formQuery = $this->databaseHelper->select(
            'customer_form_attribute',
            ['form_code']
        );

        $formQuery->where(
            'attribute_id = ?',
            $attribute->getId()
        );

        $customerFormCodes = $this->databaseHelper->fetchCol($formQuery);

        $newCustomerFormCodes = $attribute->getData('customer_forms');

        $addedCustomerFormCodes = array_diff(
            $newCustomerFormCodes,
            $customerFormCodes
        );

        if ($addedCustomerFormCodes) {
            foreach ($addedCustomerFormCodes as $customerFormCode) {
                $this->databaseHelper->createTableData(
                    $dbAdapter,
                    'customer_form_attribute',
                    ['form_code' => $customerFormCode, 'attribute_id' => $attribute->getId()]
                );
            }
        }

        $deletedCustomerFormCodes = array_diff(
            $customerFormCodes,
            $newCustomerFormCodes
        );

        if ($deletedCustomerFormCodes) {
            foreach ($deletedCustomerFormCodes as $customerFormCode) {
                $this->databaseHelper->deleteTableData(
                    $dbAdapter,
                    'customer_form_attribute',
                    sprintf(
                        'form_code = "%s" and attribute_id = %d',
                        $customerFormCode,
                        $attribute->getId()
                    )
                );
            }
        }

        $entityAttributeQuery = $this->databaseHelper->select(
            'eav_entity_attribute',
            ['entity_attribute_id']
        );

        $entityAttributeQuery->where(
            'attribute_id = ?',
            $attribute->getId()
        );

        $entityAttributeId = $this->databaseHelper->fetchOne($entityAttributeQuery);

        if (! $entityAttributeId) {
            $entityType = $this->entityTypeHelper->getCustomerEntityType();

            $attributeSetId = $entityType->getDefaultAttributeSetId();

            $attributeSet = $this->attributeHelper->getAttributeSetById($this->variables->intValue($attributeSetId));

            $attributeGroupId = $attributeSet->getDefaultGroupId();

            $this->databaseHelper->createTableData(
                $dbAdapter,
                'eav_entity_attribute',
                [
                    'entity_type_id'     => $entityType->getEntityTypeId(),
                    'attribute_set_id'   => $attributeSetId,
                    'attribute_group_id' => $attributeGroupId,
                    'attribute_id'       => $attribute->getId(),
                    'sort_order'         => $attribute->getData('sort_order')
                ]
            );
        }
    }
}
