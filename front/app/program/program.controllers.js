/**
 * This file defines the list controllers of program module
 * 
 * @param {controller} presentationController
 * @param {controller} commissioningController
 * @param {controller} generalTermsController
 * @param {controller} paymentController
 * @param {controller} configurationController
 * @param {controller} administrationController
 * @param {controller} sponsorshipController
 * @param {controller} affairController
 * @param {controller} journalController
 * @returns {controller}
 */
define([
    "./controllers/presentationController",
    "./controllers/commissioningController",
    "./controllers/generalTermsController",
    "./controllers/paymentController",
    "./controllers/configurationController",
    "./controllers/administrationController",
    "./controllers/sponsorshipController",
    "./controllers/affairController",
    "./controllers/journalController"

], function (
        presentationController,
        commissioningController,
        generalTermsController,
        paymentController,
        configurationController,
        administrationController,
        sponsorshipController,
        affairController, 
        journalController) {
    'use strict';
    var controllersModuleName = "program.controllers";
    angular.module(controllersModuleName, [])
            .controller("program:presentationController", presentationController)
            .controller("program:commissioningController", commissioningController)
            .controller("program:generalTermsController", generalTermsController)
            .controller("program:paymentController", paymentController)
            .controller("program:configurationController", configurationController)
            .controller("program:administrationController", administrationController)
            .controller("program:sponsorshipController", sponsorshipController)
            .controller("program:affairController", affairController) 
            .controller("program:journalController", journalController) ;

    return controllersModuleName;
});