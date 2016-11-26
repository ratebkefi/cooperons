/**
 * This file defines the absolute value filter of a number
 *
 * @category filter
 * @package mandatiare
 * @author Fondative <devteam@fondative.com>
 * @copyright 2015-2016 Fondative
 * @version 0.0.1
 * @since File available since Release 0.0.1
 */

define([], function () {
    'use strict';
    abs.$inject = ['$filter'];
    function abs($filter) {
        return function (number) {
            return Math.abs(number);
        };
    }
    return abs;
});