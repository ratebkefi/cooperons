/**
 *  This file defines the list of controllers and services of home module
 * 
 * @param {type} userControllers
 * @returns {module}
 */
define([
    "./user.controllers",
    "./user.routes",
    "./user.services"
], function (userControllers, userRoutes, userServices) {
    'use strict';

    var moduleName = "user";

    angular.module(moduleName,
            [
                userControllers,
                userRoutes,
                userServices
            ]);

    return moduleName;
});

