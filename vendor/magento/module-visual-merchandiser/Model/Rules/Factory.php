<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\VisualMerchandiser\Model\Rules;

class Factory
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @deprecated 100.2.0 This property exists to provide backward compatibility.
     *             Avoid usage of injected shared model instance.
     *             Create a new instance with a factory instead.
     * @see self::attribiteFactory
     * @var \Magento\Catalog\Model\ResourceModel\Eav\Attribute
     */
    protected $attribute;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Eav\AttributeFactory
     */
    private $attributeFactory;

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Catalog\Model\ResourceModel\Eav\Attribute $attribute
     * @param \Magento\Catalog\Model\ResourceModel\Eav\AttributeFactory $attributeFactory
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Catalog\Model\ResourceModel\Eav\Attribute $attribute,
        \Magento\Catalog\Model\ResourceModel\Eav\AttributeFactory $attributeFactory = null
    ) {
        $this->objectManager = $objectManager;
        $this->attribute = $attribute;
        $this->attributeFactory = $attributeFactory ?: $objectManager->get(
            \Magento\Catalog\Model\ResourceModel\Eav\AttributeFactory::class
        );
    }

    /**
     * @param string $str
     * @param array $noStrip
     * @return string
     */
    public static function classCase($str, array $noStrip = [])
    {
        // non-alpha and non-numeric characters become spaces
        $str = preg_replace('/[^a-z0-9' . implode("", $noStrip) . ']+/i', ' ', $str);
        $str = trim($str);

        // uppercase the first character of each word
        $str = ucwords($str);
        $str = str_replace(" ", "", $str);

        return $str;
    }

    /**
     * @param string $attributeCode
     * @return bool
     */
    public function isBool($attributeCode)
    {
        // TODO: Need some better idea for specifying boolean datatype
        return in_array($attributeCode, [
            'links_purchased_separately'
        ]);
    }

    /**
     * @param array $rule
     * @return \Magento\VisualMerchandiser\Model\Rules\RuleInterface
     */
    public function create(array $rule)
    {
        $attribute = $this->attributeFactory->create()->loadByCode(
            \Magento\Catalog\Model\Product::ENTITY,
            $rule['attribute']
        );

        $className = self::classCase($rule['attribute']);
        $className = '\Magento\VisualMerchandiser\Model\Rules\Rule\\' . $className;
        $args = [
            'rule' => $rule,
            'attribute' => $attribute,
        ];

        // Try load attribute type by class name
        // or if it does not exist, load the Factory class
        if (class_exists($className)) {
            $handler = $this->objectManager->create($className, $args);
        } else {
            $class = null;

            if (!$attribute->usesSource()) {
                if ($this->isBool($rule['attribute'])) {
                    $class = \Magento\VisualMerchandiser\Model\Rules\Rule\Boolean::class;
                } else {
                    $class = \Magento\VisualMerchandiser\Model\Rules\Rule\Literal::class;
                }
            }

            if ($attribute->usesSource()) {
                $class = \Magento\VisualMerchandiser\Model\Rules\Rule\Source::class;
            }

            $handler = $this->objectManager->create($class, $args);
        }

        return $handler->get();
    }
}
