/**
 * This file defines the list controllers of member module
 * 
 * @param {controller} homeController
 * @param {controller} programController
 * @param {controller} filleulController
 * @param {controller} accountController
 * @param {controller} pointController
 * @returns {String}
 */
define([
    "./controllers/homeController",
    "./controllers/programController",
    "./controllers/filleulController" ,
    "./controllers/accountController", 
    "./controllers/pointController"
], function (homeController, programController, filleulController, accountController, pointController) {
    'use strict';
    var controllersModuleName = "member.controllers";
    angular.module(controllersModuleName, [])
        .controller("member:homeController", homeController)
        .controller("member:programController", programController)
        .controller("member:filleulController", filleulController)
        .controller("member:accountController", accountController)
        .controller("member:pointController", pointController);

    return controllersModuleName;
});