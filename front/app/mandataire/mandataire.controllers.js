/**
 * This file defines the list controllers of corporate module
 * 
 * @param {controller} provisionController
 * @param {controller} paymentController
 * @param {controller} administrationController
 * @param {controller} partyAdministrationController
 * @returns {controller}
 */
define([
    "./controllers/provisionController",
    "./controllers/paymentController",
    "./controllers/administrationController",
    "./controllers/partyAdministrationController"
], function (provisionController, paymentController, administrationController, partyAdministrationController) {
    'use strict';
    var controllersModuleName = "mandataire.controllers";
    angular.module(controllersModuleName, [])
            .controller("mandataire:provisionController", provisionController)
            .controller("mandataire:paymentController", paymentController)
            .controller("mandataire:administrationController", administrationController)
            .controller("mandataire:partyAdministrationController", partyAdministrationController);

    return controllersModuleName;
});