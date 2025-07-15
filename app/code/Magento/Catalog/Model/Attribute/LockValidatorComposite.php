<?php
/**
 * Copyright 2014 Adobe
 * All Rights Reserved.
 */
namespace Magento\Catalog\Model\Attribute;

class LockValidatorComposite implements LockValidatorInterface
{
    /**
     * @var LockValidatorInterface[]
     */
    protected $validators = [];

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param array $validators
     * @throws \InvalidArgumentException
     */
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager, array $validators = [])
    {
        foreach ($validators as $validator) {
            if (!is_subclass_of($validator, \Magento\Catalog\Model\Attribute\LockValidatorInterface::class)) {
                throw new \InvalidArgumentException($validator . ' does not implements LockValidatorInterface');
            }
            $this->validators[] = $objectManager->get($validator);
        }
    }

    /**
     * Check attribute lock state
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @param null $attributeSet
     * @throws \Magento\Framework\Exception\LocalizedException
     *
     * @return void
     */
    public function validate(\Magento\Framework\Model\AbstractModel $object, $attributeSet = null)
    {
        foreach ($this->validators as $validator) {
            $validator->validate($object, $attributeSet);
        }
    }
}
