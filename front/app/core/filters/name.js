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

    /*ngInject*/
    function name() {

        return function (object) {
            var name = '';

            if (object.firstName || object.lastName) {
                name = object.firstName + ' ' + object.lastName;
            } else if (object.member) {
                name = object.member.firstName + ' ' + object.member.lastName;
            } else if (object.invitation) {
                name = object.invitation.firstName + ' ' + object.invitation.lastName;

            }
            return name;
        };
    }

    return name;
});