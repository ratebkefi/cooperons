/**
 * @ngdoc module
 * @name prototype:prototype.controllers
 * @description This file for declaring controllers of prototype module
 */

define([
    "./controllers/contratController",
    "./controllers/habilitationController",
], function (contratController, habilitationController) {
    "use strict";

    var controllersModuleName = "prototype.controllers";

    angular.module(controllersModuleName, [])
        .controller("prototype:contratController", contratController)
        .controller("prototype:habilitationController", habilitationController);

    return controllersModuleName;
});