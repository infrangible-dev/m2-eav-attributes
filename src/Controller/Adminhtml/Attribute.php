<?php /** @noinspection PhpDeprecationInspection */

declare(strict_types=1);

namespace Infrangible\EavAttributes\Controller\Adminhtml;

use Infrangible\Core\Helper\EntityType;
use Infrangible\Core\Helper\Registry;
use Laminas\Validator\Regex;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Forward;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\Backend\Model\View\Result\Page;
use Magento\Catalog\Helper\Product;
use Magento\Catalog\Model\Product\Url;
use Magento\Eav\Model\Adminhtml\System\Config\Source\Inputtype\ValidatorFactory;
use Magento\Eav\Model\Entity;
use Magento\Eav\Model\Entity\Attribute\AbstractAttribute;
use Magento\Eav\Model\Validator\Attribute\Code;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Cache\FrontendInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;
use Magento\Framework\Serialize\Serializer\FormData;
use Magento\Framework\Validator\ValidateException;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\Layout;
use Magento\Framework\View\LayoutFactory;
use Magento\Framework\View\Result\PageFactory;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2025 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
abstract class Attribute extends Action
{
    public const DEFAULT_MESSAGE_KEY = 'message';

    /** @var Registry */
    protected $registryHelper = null;

    /** @var FrontendInterface */
    protected $attributeLabelCache;

    /** @var PageFactory */
    protected $resultPageFactory;

    /** @var ForwardFactory */
    protected $resultForwardFactory;

    /** @var Url */
    protected $url;

    /** @var EntityType */
    protected $entityTypeHelper;

    /** @var ValidatorFactory */
    protected $validatorFactory;

    /** @var Product */
    protected $productHelper;

    /** @var LayoutFactory */
    protected $layoutFactory;

    /** @var JsonFactory */
    protected $resultJsonFactory;

    /** @var FormData */
    protected $formDataSerializer;

    /** @var Code */
    protected $attributeCodeValidator;

    /** @var array */
    protected $multipleAttributeList;

    /** @var string */
    private $entityTypeId;

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
        FormData $formDataSerializer = null,
        Code $attributeCodeValidator = null,
        array $multipleAttributeList = []
    ) {
        $this->registryHelper = $registryHelper;
        $this->attributeLabelCache = $attributeLabelCache;
        $this->resultPageFactory = $resultPageFactory;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->url = $url;
        $this->entityTypeHelper = $entityTypeHelper;
        $this->validatorFactory = $validatorFactory;
        $this->productHelper = $productHelper;
        $this->layoutFactory = $layoutFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->formDataSerializer = $formDataSerializer ? : ObjectManager::getInstance()->get(FormData::class);
        $this->attributeCodeValidator = $attributeCodeValidator ? : ObjectManager::getInstance()->get(Code::class);
        $this->multipleAttributeList = $multipleAttributeList;

        parent::__construct($context);
    }

    /**
     * @throws LocalizedException
     * @noinspection PhpMissingReturnTypeInspection
     */
    public function dispatch(RequestInterface $request)
    {
        $entityType = $this->entityTypeHelper->getEntityType($this->getEntityTypeId());

        $this->entityTypeId = $entityType->getEntityTypeId();

        return parent::dispatch($request);
    }

    /**
     * @param Phrase|string|null $title
     */
    abstract protected function createEntityTypeActionPage($title = null): Page;

    /**
     * @param Phrase[] $breadcrumbs
     */
    protected function createActionPage(array $breadcrumbs, string $menuId): Page
    {
        /** @var Page $resultPage */
        $resultPage = $this->resultPageFactory->create();

        $resultPage->setActiveMenu($menuId);

        $title = null;

        foreach ($breadcrumbs as $breadcrumb) {
            if ($breadcrumb) {
                $resultPage->addBreadcrumb(
                    $breadcrumb,
                    $breadcrumb
                );

                $title = $breadcrumb;
            }
        }

        if ($title) {
            $resultPage->getConfig()->getTitle()->prepend($title);
        }

        return $resultPage;
    }

    abstract protected function getEntityTypeId(): string;

    abstract protected function createModel(): Entity\Attribute;

    abstract protected function getIndexClassName(): string;

    protected function indexAttribute(): Page
    {
        $resultPage = $this->createEntityTypeActionPage();

        /** @var AbstractBlock $attributeBlock */
        $attributeBlock = $resultPage->getLayout()->createBlock($this->getIndexClassName());

        $resultPage->addContent($attributeBlock);

        return $resultPage;
    }

    protected function newAttribute(): Forward
    {
        return $this->resultForwardFactory->create()->forward('edit');
    }

    abstract protected function getIndexPath(): string;

    protected function getPresentationInputType(AbstractAttribute $attribute): ?string
    {
        return $attribute->getFrontendInput();
    }

    protected function addEntityAttributeData(Entity\Attribute $attribute): void
    {
    }

    protected function editAttribute()
    {
        $id = $this->getRequest()->getParam('attribute_id');

        $attribute = $this->createModel();

        $attribute->setEntityTypeId($this->entityTypeId);

        if ($id) {
            $attribute->load($id);

            if (! $attribute->getId()) {
                $this->messageManager->addErrorMessage(__('This attribute no longer exists.'));

                $resultRedirect = $this->resultRedirectFactory->create();

                return $resultRedirect->setPath($this->getIndexPath());
            }

            // entity type check
            if ($attribute->getEntityTypeId() != $this->entityTypeId) {
                $this->messageManager->addErrorMessage(__('This attribute cannot be edited.'));

                $resultRedirect = $this->resultRedirectFactory->create();

                return $resultRedirect->setPath($this->getIndexPath());
            }
        }

        // set entered data if was error when we do save
        $data = $this->_session->getData(
            'attribute_data',
            true
        );

        if (! empty($data)) {
            $attribute->addData($data);
        }

        $attribute->setFrontendInput($this->getPresentationInputType($attribute));

        $attributeData = $this->getRequest()->getParam('attribute');

        if (! empty($attributeData) && $id === null) {
            $attribute->addData($attributeData);
        }

        $this->addEntityAttributeData($attribute);

        $this->registryHelper->register(
            'entity_attribute',
            $attribute
        );

        return $this->createEntityTypeActionPage($id ? $attribute->getName() : __('New Attribute'));
    }

    abstract protected function getReservedAttributeCodes(): array;

    /**
     * @throws ValidateException
     * @throws LocalizedException
     */
    public function validateAttribute(): Json
    {
        $response = new DataObject();

        $response->setData(
            'error',
            false
        );

        try {
            $optionsData = $this->formDataSerializer->unserialize(
                $this->getRequest()->getParam(
                    'serialized_options',
                    '[]'
                )
            );
        } catch (\InvalidArgumentException $e) {
            $message = __(
                "The attribute couldn't be validated due to an error. Verify your information and try again. " .
                'If the error persists, please try again later.'
            );

            $this->setMessageToResponse(
                $response,
                [$message]
            );

            $response->setData(
                'error',
                true
            );
        }

        $attributeCode = $this->getRequest()->getParam('attribute_code');
        $frontendLabel = $this->getRequest()->getParam('frontend_label');
        $attributeId = $this->getRequest()->getParam('attribute_id');

        /** @var \Magento\Catalog\Model\ResourceModel\Eav\Attribute $attribute */
        $attribute = $this->createModel();

        if ($attributeId) {
            $attribute->load($attributeId);

            $attributeCode = $attribute->getAttributeCode();
        } else {
            $attributeCode = $attributeCode ? : $this->generateCode($frontendLabel[ 0 ]);

            $attribute->loadByCode(
                $this->entityTypeId,
                $attributeCode
            );
        }

        if (! $attribute->getId() && in_array(
                $attributeCode,
                $this->getReservedAttributeCodes(),
                true
            )) {
            $message = __(
                'Code (%1) is a reserved key and cannot be used as attribute code.',
                $attributeCode
            );

            $this->setMessageToResponse(
                $response,
                [$message]
            );

            $response->setData(
                'error',
                true
            );
        }

        if ($attribute->getId() && ! $attributeId) {
            $message = strlen($this->getRequest()->getParam('attribute_code')) ?
                __('An attribute with this code already exists.') : __(
                    'An attribute with the same code (%1) already exists.',
                    $attributeCode
                );

            $this->setMessageToResponse(
                $response,
                [$message]
            );

            $response->setData(
                'error',
                true
            );
            $response->setData(
                'category_attribute',
                $attribute->toArray()
            );
        }

        if (! $this->attributeCodeValidator->isValid($attributeCode)) {
            $this->setMessageToResponse(
                $response,
                $this->attributeCodeValidator->getMessages()
            );

            $response->setData(
                'error',
                true
            );
        }

        $multipleOption = $this->getRequest()->getParam('frontend_input');

        $multipleOption = (null === $multipleOption) ? 'select' : $multipleOption;

        if (array_key_exists(
            $multipleOption,
            $this->multipleAttributeList
        )) {
            $options = $optionsData[ $this->multipleAttributeList[ $multipleOption ] ] ?? null;

            $this->checkUniqueOption(
                $response,
                $options
            );

            $valueOptions = (isset($options[ 'value' ]) && is_array($options[ 'value' ])) ? $options[ 'value' ] : [];

            foreach (array_keys($valueOptions) as $key) {
                if (! empty($options[ 'delete' ][ $key ])) {
                    unset($valueOptions[ $key ]);
                }
            }

            $this->checkEmptyOption(
                $response,
                $valueOptions
            );
        }

        return $this->resultJsonFactory->create()->setJsonData($response->toJson());
    }

    private function setMessageToResponse($response, $messages): void
    {
        $messageKey = $this->getRequest()->getParam(
            'message_key',
            static::DEFAULT_MESSAGE_KEY
        );

        if ($messageKey === static::DEFAULT_MESSAGE_KEY) {
            $messages = reset($messages);
        }

        $response->setData(
            $messageKey,
            $messages
        );
    }

    private function isUniqueAdminValues(array $optionsValues, array $deletedOptions): array
    {
        $adminValues = [];

        foreach ($optionsValues as $optionKey => $values) {
            if (! (isset($deletedOptions[ $optionKey ]) && $deletedOptions[ $optionKey ] === '1')) {
                $adminValues[] = reset($values);
            }
        }

        $uniqueValues = array_unique($adminValues);

        return array_diff_assoc(
            $adminValues,
            $uniqueValues
        );
    }

    private function checkUniqueOption(DataObject $response, array $options = null): void
    {
        if (is_array($options) && ! empty($options[ 'value' ]) && ! empty($options[ 'delete' ])) {
            $duplicates = $this->isUniqueAdminValues(
                $options[ 'value' ],
                $options[ 'delete' ]
            );

            if (! empty($duplicates)) {
                $this->setMessageToResponse(
                    $response,
                    [
                        __(
                            'The value of Admin must be unique. (%1)',
                            implode(
                                ', ',
                                $duplicates
                            )
                        )
                    ]
                );

                $response->setData(
                    'error',
                    true
                );
            }
        }
    }

    private function checkEmptyOption(DataObject $response, array $optionsForCheck = null): void
    {
        foreach ($optionsForCheck as $optionValues) {
            if (isset($optionValues[ 0 ]) && trim((string)$optionValues[ 0 ]) == '') {
                $this->setMessageToResponse(
                    $response,
                    [__("The value of Admin scope can't be empty.")]
                );

                $response->setData(
                    'error',
                    true
                );
            }
        }
    }

    protected function convertPresentationDataToInputType(array $data): array
    {
        return $data;
    }

    protected function saveEntityAttributeData(Entity\Attribute $attribute): void
    {
    }

    /**
     * @throws \Exception
     */
    protected function saveAttribute(): ResultInterface
    {
        try {
            $optionData = $this->formDataSerializer->unserialize(
                $this->getRequest()->getParam(
                    'serialized_options',
                    '[]'
                )
            );
        } catch (\InvalidArgumentException $exception) {
            $this->messageManager->addErrorMessage(
                __(
                    "The attribute couldn't be saved due to an error. Verify your information and try again. " .
                    'If the error persists, please try again later.'
                )
            );

            return $this->returnResult(
                '*/*/edit',
                ['_current' => true],
                ['error' => true]
            );
        }

        $request = $this->getRequest();

        $data = $request instanceof Http ? $request->getPostValue() : [];

        $data = array_replace_recursive(
            $data,
            $optionData
        );

        if ($data) {
            $attributeId = $this->getRequest()->getParam('attribute_id');

            if (! empty($data[ 'attribute_id' ]) && $data[ 'attribute_id' ] != $attributeId) {
                $attributeId = $data[ 'attribute_id' ];
            }

            $attribute = $this->createModel();

            if ($attributeId) {
                $attribute->load($attributeId);
            }

            $attributeCode =
                $attribute->getId() ? $attribute->getAttributeCode() : $this->getRequest()->getParam('attribute_code');

            if (! $attributeCode) {
                $frontendLabel = $this->getRequest()->getParam('frontend_label')[ 0 ] ?? '';
                $attributeCode = $this->generateCode($frontendLabel);
            }

            $data[ 'attribute_code' ] = $attributeCode;

            //validate frontend_input
            if (isset($data[ 'frontend_input' ])) {
                $inputType = $this->validatorFactory->create();

                if (! $inputType->isValid($data[ 'frontend_input' ])) {
                    foreach ($inputType->getMessages() as $message) {
                        $this->messageManager->addErrorMessage($message);
                    }

                    return $this->returnResult(
                        '*/*/edit',
                        ['attribute_id' => $attributeId, '_current' => true],
                        ['error' => true]
                    );
                }
            }

            $data = $this->convertPresentationDataToInputType($data);

            if ($attributeId) {
                if (! $attribute->getId()) {
                    $this->messageManager->addErrorMessage(__('This attribute no longer exists.'));

                    return $this->returnResult(
                        '*/*/',
                        [],
                        ['error' => true]
                    );
                }
                // entity type check
                if ($attribute->getEntityTypeId() != $this->entityTypeId || array_key_exists(
                        'backend_model',
                        $data
                    )) {

                    $this->messageManager->addErrorMessage(__('We can\'t update the attribute.'));

                    /** @noinspection PhpUndefinedMethodInspection */
                    $this->_session->setAttributeData($data);

                    return $this->returnResult(
                        '*/*/',
                        [],
                        ['error' => true]
                    );
                }

                $data[ 'attribute_code' ] = $attribute->getAttributeCode();
                $data[ 'is_user_defined' ] = $attribute->getIsUserDefined();
                $data[ 'frontend_input' ] = $data[ 'frontend_input' ] ?? $attribute->getFrontendInput();
            } else {
                $data[ 'source_model' ] = $this->productHelper->getAttributeSourceModelByInputType(
                    $data[ 'frontend_input' ]
                );
                $data[ 'backend_model' ] = $this->productHelper->getAttributeBackendModelByInputType(
                    $data[ 'frontend_input' ]
                );

                if ($attribute->getIsUserDefined() === null) {
                    $data[ 'backend_type' ] = $attribute->getBackendTypeByInput($data[ 'frontend_input' ]);
                }
            }

            $defaultValueField = $attribute->getDefaultValueByInput($data[ 'frontend_input' ]);

            if ($defaultValueField) {
                $data[ 'default_value' ] = $this->getRequest()->getParam($defaultValueField);
            }

            if ($attribute->getBackendType() == 'static' && ! $attribute->getIsUserDefined()) {
                $data[ 'frontend_class' ] = $attribute->getFrontendClass();
            }

            unset($data[ 'entity_type_id' ]);

            $attribute->addData($data);

            if (! $attributeId) {
                $attribute->setEntityTypeId($this->entityTypeId);
                $attribute->setIsUserDefined(1);
            }

            try {
                $this->registryHelper->register(
                    'entity_attribute',
                    $attribute
                );

                $attribute->save();

                $this->saveEntityAttributeData($attribute);

                $this->messageManager->addSuccessMessage(__('You saved the attribute.'));

                $this->attributeLabelCache->clean();

                /** @noinspection PhpUndefinedMethodInspection */
                $this->_session->setAttributeData(false);

                if ($this->getRequest()->getParam(
                    'back',
                    false
                )) {
                    return $this->returnResult(
                        '*/*/edit',
                        ['attribute_id' => $attribute->getId(), '_current' => true],
                        ['error' => false]
                    );
                }

                return $this->returnResult(
                    '*/*/',
                    [],
                    ['error' => false]
                );
            } catch (\Exception $exception) {
                $this->messageManager->addErrorMessage($exception->getMessage());

                if ($attributeId === null) {
                    unset($data[ 'frontend_input' ]);
                }

                /** @noinspection PhpUndefinedMethodInspection */
                $this->_session->setAttributeData($data);

                return $this->returnResult(
                    '*/*/edit',
                    ['attribute_id' => $attributeId, '_current' => true],
                    ['error' => true]
                );
            }
        }

        return $this->returnResult(
            '*/*/',
            [],
            ['error' => true]
        );
    }

    private function returnResult($path = '', array $params = [], array $response = []): ResultInterface
    {
        if ($this->isAjax()) {
            $layout = $this->layoutFactory->create();

            if ($layout instanceof Layout) {
                $layout->initMessages();
            }

            $response[ 'messages' ] = [$layout->getMessagesBlock()->getGroupedHtml()];
            $response[ 'params' ] = $params;

            /** @var Json $json */
            $json = $this->resultFactory->create(ResultFactory::TYPE_JSON);

            return $json->setData($response);
        }

        /** @var Redirect $redirect */
        $redirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        return $redirect->setPath(
            $path,
            $params
        );
    }

    private function isAjax(): bool
    {
        $isAjax = $this->getRequest()->getParam('isAjax');

        return $isAjax === null ? false : $isAjax;
    }

    protected function deleteAttribute(): Redirect
    {
        $id = $this->getRequest()->getParam('attribute_id');

        $resultRedirect = $this->resultRedirectFactory->create();

        if ($id) {
            $attribute = $this->createModel();

            // entity type check
            $attribute->load($id);

            if ($attribute->getEntityTypeId() != $this->entityTypeId) {
                $this->messageManager->addErrorMessage(__('We can\'t delete the attribute.'));

                return $resultRedirect->setPath('*/*/');
            }

            try {
                $attribute->delete();

                $this->messageManager->addSuccessMessage(__('You deleted the attribute.'));

                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());

                return $resultRedirect->setPath(
                    '*/*/edit',
                    ['attribute_id' => $this->getRequest()->getParam('attribute_id')]
                );
            }
        }

        $this->messageManager->addErrorMessage(__('We can\'t find an attribute to delete.'));

        return $resultRedirect->setPath('*/*/');
    }

    protected function generateCode(string $label): string
    {
        $code = substr(
            preg_replace(
                '/[^a-z_0-9]/',
                '_',
                $this->url->formatUrlKey($label)
            ),
            0,
            30
        );

        $validatorAttrCode = new Regex(['pattern' => '/^[a-z][a-z_0-9]{0,29}[a-z0-9]$/']);

        if (! $validatorAttrCode->isValid($code)) {
            // md5() here is not for cryptographic use.
            // phpcs:ignore Magento2.Security.InsecureFunction
            $code = 'attr_' . ($code ? : substr(
                    md5((string)time()),
                    0,
                    8
                ));
        }

        return $code;
    }
}
