<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 3/6/13
 * Time: 3:13 PM
 */

/*`  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fieldName` varchar(200) DEFAULT NULL,
  `fieldMachineName` varchar(200) DEFAULT NULL,
  `fieldDefaultValue` text,
  `fieldPostfix` text,
  `fieldDisplayTemplate` text,
  `fieldOrder` int(11) DEFAULT NULL,
  `fieldType` int(11) DEFAULT NULL COMMENT 'fk to fieldTypes',
  `configData` text,
  `status` tinyint(1) DEFAULT '0',
*/

namespace Fields\Model;

class Field
{
    public $id;
    public $fieldName;
    public $fieldMachineName;
    public $fieldType;
    public $fieldConfigData = array();
    public $filters = array();
    public $validators = array();
    public $fieldDefaultValue;
    public $fieldPostfix;
    public $fieldDisplayTemplate;
    public $fieldOrder;
    public $status;
    public $fieldPrefix;
    public $entityType;
    public $collection = 0;

    /**
     * @return int
     */
    public function getCollection()
    {
        return $this->collection;
    }

    /**
     * @param int $collection
     */
    public function setCollection($collection)
    {
        $this->collection = $collection;
    }


    /**
     * @param mixed $entityType
     */
    public function setEntityType($entityType)
    {
        $this->entityType = $entityType;
    }

    /**
     * @return mixed
     */
    public function getEntityType()
    {
        return $this->entityType;
    }


    public function setFilters($filters)
    {
        $this->filters = $filters;
    }

    public function getFilters()
    {
        return $this->filters;
    }

    public function setValidators($validators)
    {
        $this->validators = $validators;
    }

    public function getValidators()
    {
        return $this->validators;
    }

    public function setFieldPrefix($fieldPrefix)
    {
        $this->fieldPrefix = $fieldPrefix;
    }

    public function getFieldPrefix()
    {
        return $this->fieldPrefix;
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setFieldDefaultValue($fieldDefaultValue)
    {
        $this->fieldDefaultValue = $fieldDefaultValue;
    }

    public function getFieldDefaultValue()
    {
        return $this->fieldDefaultValue;
    }

    public function setFieldDisplayTemplate($fieldDisplayTemplate)
    {
        $this->fieldDisplayTemplate = $fieldDisplayTemplate;
    }

    public function getFieldDisplayTemplate()
    {
        return $this->fieldDisplayTemplate;
    }

    public function setFieldOrder($fieldOrder)
    {
        $this->fieldOrder = $fieldOrder;
    }

    public function getFieldOrder()
    {
        return $this->fieldOrder;
    }

    public function setFieldPostfix($fieldPostfix)
    {
        $this->fieldPostfix = $fieldPostfix;
    }

    public function getFieldPostfix()
    {
        return $this->fieldPostfix;
    }

    public function setFieldConfigData($configData)
    {
        $this->fieldConfigData = $configData;
    }

    public function getFieldConfigData()
    {
        return $this->fieldConfigData;
    }

    public function setFieldMachineName($fieldMachineName)
    {
        $this->fieldMachineName = $fieldMachineName;
    }

    public function getFieldMachineName()
    {
        return $this->fieldMachineName;
    }

    public function setFieldName($fieldName)
    {
        $this->fieldName = $fieldName;
    }

    public function getFieldName()
    {
        return $this->fieldName;
    }

    public function setFieldType($fieldType)
    {
        $this->fieldType = $fieldType;
    }

    public function getFieldType()
    {
        return $this->fieldType;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }
}
