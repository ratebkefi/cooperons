/**
 * This file defines the filter fo price
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
    displayPrice.$inject = ['$filter'];
    function displayPrice($filter) {

        return function (price, hundred, decimal, numberDecimal) {
            if (typeof(hundred) === 'undefined') {
                hundred = ' ';
            }
            if (typeof(decimal) === 'undefined') {
                decimal = ',';
            }
            if (typeof(numberDecimal) === 'undefined') {
                numberDecimal = 2;
            }

            price = $filter('number')(price, numberDecimal);

            if (price) {
                for (var i = 0; i <= (price.match(/,/g) || []).length; i++) {
                    price = price.replace(",", hundred);
                }
                return price.replace(".", decimal);
            }
        };
    }

    return displayPrice;
});