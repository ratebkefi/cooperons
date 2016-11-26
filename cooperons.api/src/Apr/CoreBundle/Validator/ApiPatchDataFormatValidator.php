<?php

/**
 * This file defines the data format validator for path method
 *
 * @category CoreBundle
 * @package Validator
 * @author Fondative <devteam@fondative.com>
 * @copyright 2015-2016 Fondative
 * @version 1.0.0
 * @since File available since Release 1.0.0
 */

namespace Apr\CoreBundle\Validator;

use Apr\CoreBundle\ApiException\ApiException;
use Apr\CoreBundle\Validator\Validator;

/**
 * Class ApiPatchDataFormatValidator for API services
 *
 * @category CoreBundle
 * @package Validator
 * @author Fondative <devteam@fondative.com>
 * @copyright 2015-2016 Fondative
 * @version 1.0.0
 * @since Class available since Release 1.0.0
 *
 */
class ApiPatchDataFormatValidator
{

    private $patchFormat = array();

    public function __construct($patchFormat)
    {
        $this->patchFormat = $patchFormat;
    }

    /**
     * Validate data format.
     * Data format must be [{op: 'operation name', path: 'resource path', value: 'updated value'}+]
     * Deprecated
     *
     * @param $data
     * @param $resource
     * @return array
     * @throws \Exception
     */
    public function validateDataFormat($data, $resource)
    {
        if (!is_array($data) || count($data) === 0) {
            throw new ApiException(40060);
        }

        if (!isset($this->patchFormat[$resource])) {
            throw new \Exception('No structure defined for «' . $resource . '» resource');
        }

        $validatedPatch = array();
        foreach ($data as $patch) {
            if (!isset($patch->op) || !isset($patch->path)) {
                throw new ApiException(40060);
            }
            $operation = $this->getOperation($patch, $this->patchFormat[$resource]);

            $validatedPatch[] = $this->validatePatch($patch, $operation);
        }
        return $validatedPatch;

    }

    /**
     * Get operation structure from yml file
     *
     * @param $patch
     * @param $structures
     * @return array patch operation
     */
    public function getOperation($patch, $structures)
    {
        foreach ($structures as $key => $structure) {
            if ($structure['op'] === $patch->op && $structure['path'] === $patch->path) {
                return $structures[$key];
            }
        }
        throw new ApiException(40061, array('errors' => 'Unknown operation «' . $patch->op . '» with path «' . $patch->path . '»'));
    }

    /**
     * Validate data format of patch
     *
     * @param $patch
     * @param $operation
     * @return bool
     */
    public function validatePatch($patch, $operation)
    {
        $params = isset($operation['params']) ? $operation['params'] : array();
        $requirements = isset($operation['requirements']) ? $operation['requirements'] : array();

        foreach ($params as $param) {
            if (is_array($param)) {
                foreach ($param as $key => $value) {
                    if (!isset($patch->$key)) {
                        $patch->$key = $value;
                    } else {
                        $patch->$key = trim($patch->$key);
                        if (isset($requirements[$key])) {
                            $this->validateType($patch, $key, $requirements[$key]);
                        }
                    }
                }
            } elseif (isset($patch->$param)) {
                $patch->$param = trim($patch->$param);
                if (isset($requirements[$param])) {
                    $this->validateType($patch, $param, $requirements[$param]);
                }
            } else {
                throw new ApiException(40061, array('errors' => '«' . $param . '» parameter is required for «' . $patch->op . '» operation'));
            }
        }
        return $patch;
    }

    /**
     * Validate parameter type
     *
     * @param $patch
     * @param $param
     * @param $type
     * @return bool
     */
    public function validateType($patch, $param, $type)
    {
        $valid = Validator::validate($patch->$param, $type);

        switch ($type) {
            case 'integer':
                if (!ctype_digit((string)$patch->$param)) {
                    $valid = false;
                }
                break;
            case 'number':
                if (!is_numeric($patch->$param)) {
                    $valid = false;
                }
                break;
            case 'url':
                if (!preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i", $patch->$param)) {
                    $valid = false;
                }
                break;

        }

        if (!$valid) {
            $errorMsg = 'Parameter «' . $param . '» for «' . $patch->op . '» operation must be «' . $type . '»';
            throw new ApiException(40061, array('errors' => $errorMsg));
        }

        return true;

    }

}
