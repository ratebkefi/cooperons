/*global define, angular, list of controllers*/
define([
    "./controllers/homeController",
    "./controllers/corporateController",
    "./controllers/attestationController",
    "./controllers/giftOrderController",
    "./controllers/invoiceController",
    "./controllers/mandataireController",
    "./controllers/programController",
    "./controllers/userController",
    "./controllers/autoEntrepreneurController",
    "./controllers/previewInvoiceController"
], function (homeController, corporateController, attestationController, giftOrderController, invoiceController,
             mandataireController, programController, userController, autoEntrepreneurController, previewInvoiceController) {
    'use strict';
    var controllersModuleName = "admin.controllers";
    angular.module(controllersModuleName, [])
        .controller("admin:homeController", homeController)
        .controller("admin:corporateController", corporateController)
        .controller("admin:attestationController", attestationController)
        .controller("admin:giftOrderController", giftOrderController)
        .controller("admin:invoiceController", invoiceController)
        .controller("admin:mandataireController", mandataireController)
        .controller("admin:programController", programController)
        .controller("admin:userController", userController)
        .controller("admin:autoEntrepreneurController", autoEntrepreneurController)
        .controller("admin:previewInvoiceController", previewInvoiceController);

return controllersModuleName;
});