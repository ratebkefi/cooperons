/**
 * This file defines the list controllers of home module
 * 
 * @param {object} homeController
 * @returns {object} controllersModuleName
 */
define([
    "./controllers/shellController"
], function (ShellController) {
    'use strict';
    var controllersModuleName = "layout.controllers";
    angular.module(controllersModuleName, [])
            .controller("layout:ShellController", ShellController);

    return controllersModuleName;
});