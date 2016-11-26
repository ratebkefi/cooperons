<?php

/**
 * This file defines the validation rules of data
 *
 * @category CoreBundle
 * @package Validator
 * @author Fondative <devteam@fondative.com>
 * @copyright 2015-2016 Fondative
 * @version 1.0.0
 * @since File available since Release 1.0.0
 */

namespace Apr\CoreBundle\Validator;
use Symfony\Component\Config\Definition\Exception\Exception;

/**
 * Class Validator to defines data validation rules
 *
 * @category CoreBundle
 * @package Validator
 * @author Fondative <devteam@fondative.com>
 * @copyright 2015-2016 Fondative
 * @version 1.0.0
 * @since Class available since Release 1.0.0
 *
 */
class Validator {

    const TYPE_EMAIL = 'email';
    const TYPE_MOBILE = 'mobile';
    const TYPE_NUMBER = 'number';
    const TYPE_INTEGER = 'integer';
    const TYPE_URL = 'url';

    public static function validate($value, $validationType){
        switch($validationType){
            case self::TYPE_INTEGER:
                return \ctype_digit((string)$value);
            case self::TYPE_NUMBER:
                return \is_numeric($value);
            case self::TYPE_EMAIL:
                return \filter_var($value, FILTER_VALIDATE_EMAIL);
            case self::TYPE_MOBILE:
                $regex = "/^(\d[\s-]?)?[\(\[\s-]{0,2}?\d{3}[\)\]\s-]{0,2}?\d{3}[\s-]?\d{4}$/i";
                return preg_match($regex, $value);
            case self::TYPE_URL:
                $regex = "/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i";
                return preg_match($regex, $value);
            default:
                throw new Exception('Unknown type «' .$validationType. '»' );
        }

    }

}
