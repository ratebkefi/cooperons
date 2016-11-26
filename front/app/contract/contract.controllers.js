/**
 * This file defines the list controllers of contract module
 */
define([
    './controllers/contractListController',
    './controllers/contractController',
    './controllers/administrationController',
    './controllers/contractLegalController'
], function (ContractListController, ContractController, AdministrationController, ContractLegalController) {
    'use strict';

    var controllersModuleName = 'contract.controllers';

    angular.module(controllersModuleName, [])
        .controller('contract:ContractListController', ContractListController)
        .controller('contract:ContractController', ContractController)
        .controller('contract:AdministrationController', AdministrationController)
        .controller('contract:ContractLegalController', ContractLegalController);

    return controllersModuleName;
});
