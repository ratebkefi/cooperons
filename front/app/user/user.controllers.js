/**
 * This file defines the list controllers of corporate module
 * 
 * @param {controller} securityController
 * @returns {module}
 */
define([
    "./controllers/securityController"
], function (securityController) {
    'use strict';

    var controllersModuleName = "user.controllers";

    angular.module(controllersModuleName, [])
            .controller("user:securityController", securityController);

    return controllersModuleName;
});