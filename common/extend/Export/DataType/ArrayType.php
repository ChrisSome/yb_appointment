<?php
/**
 * This file is part of easycrm, created by PhpStorm.
 * Author: Sihuo
 * Date: 2016/11/9 10:40
 * File: ArrayType.php
 */

namespace common\extend\Export\DataType;

class ArrayType implements DataTypeInterface
{
    /** @var  string */
    private $data;

    /** @var  array */
    private $fields;

    public function __construct($data, $fields = array())
    {
        $this->data = $data;
        $this->fields = $fields;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * {@inheritdoc}
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return self::TYPE_ARRAY;
    }
}
