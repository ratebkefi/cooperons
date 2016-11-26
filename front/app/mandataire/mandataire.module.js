/**
 *  This file defines the list of controllers and services of mandataire module
 * 
 * @param {type} mandataireServices
 * @returns {String}
 */
define([
    "./mandataire.controllers",
    "./mandataire.services",
    "./mandataire.directives",
    "./mandataire.routes",
     "./mandataire.filters.js"

], function (mandataireControllers, mandataireServices, mandataireDirectives, mandataireRoutes, mandataireFilters) {
    'use strict';
    angular.module("mandataire",
            [
                mandataireControllers,
                mandataireServices,
                mandataireDirectives,
                mandataireRoutes,
                mandataireFilters
            ]);

    return "mandataire";
}); 

