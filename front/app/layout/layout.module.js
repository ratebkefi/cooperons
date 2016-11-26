/**
 *  This file defines the list of controllers and services of home module
 * 
 * @param {object} homeControllers
 * @returns {module}
 */
define([
    "./layout.controllers"
], function (layoutControllers) {
    'use strict';

    var moduleName = "layout";

    angular.module(moduleName,
            [
                layoutControllers
            ]);

    return moduleName;
});

