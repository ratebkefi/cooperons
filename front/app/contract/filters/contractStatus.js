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
    function contractStatus($filter) {

        return function (contract) {
            var status = '';
            if (contract.status == 'active') {
                if (contract.suspensionDate) {
                    status = ' Prestation en attente ...';
                } else if (contract.type == 'default') {
                    status = 'Dépôt: ' + $filter('displayPrice')(contract.depot) + ' €';
                }
            } else if (contract.status == 'cancel') {
                status = ' Résilié';
            } else if (contract.status == 'empty') {
                status = ' Contrat ?';
            } else {
                status = ' En attente ...';
            }
            return status;
        };
    }

    return contractStatus;
});