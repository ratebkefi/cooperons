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
    function allowContractsActions() {

        return function (contracts, filterContract) {
            angular.forEach(contracts, function (contract) {
                contract.actions = {};
                contract.actions.delete = contract.isRemovable;
                contract.actions.edit = (contract.status != 'standby');
                contract.actions.administration = (contract.status == 'active' && contract.mandataire);

                if (contract.invitation) {
                    contract.actions.resend = true;
                } else if (contract.status != 'standby') {
                    if (filterContract.split(':')[1] == 'owner') {
                        if (contract.status == 'active') {
                            if (contract.type == 'affair') {
                                contract.actions.report = true;
                            } else if (contract.mandataire) {
                                if (!contract.suspensionDate && contract.allServiceTypes.length > 0) {
                                    contract.actions.newSettlements = true;
                                }
                            }
                        }
                    }
                }
            });
            return contracts;
        }
    }

    return allowContractsActions;
});