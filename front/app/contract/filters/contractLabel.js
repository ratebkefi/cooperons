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
    function contractLabel() {

        return function (contract, filterContract) {
            var label = '';
            var side = filterContract.split(':')[1];
            if (contract.invitation) {
                label = contract.invitation.firstName + ' ' + contract.invitation.lastName;
            } else {
                label = ( side == 'owner') ? contract.clientLabel : contract.ownerLabel;
            }
            return label;
        };
    }

    return contractLabel;
});