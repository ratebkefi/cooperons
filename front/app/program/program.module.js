/**
 * This file defines the list of controllers and services of program module
 * 
 * @param {module} programServices
 * @returns {String}
 */
define([
    "./program.controllers.js",
    "./program.routes.js",
    "./program.services.js",
    "./program.directives.js" 

], function (programControllers, programRoutes, programServices, programDirectives) {
    'use strict';
    angular.module("program",
            [
                programControllers,
                programRoutes,
                programServices,
                programDirectives
            ]);

    return "program";
});

