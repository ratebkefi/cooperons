/**
 * This file defines the list of controllers and services of program module
 *
 * @param {module} programServices
 * @returns {String}
 */
define([
    "./public.controllers",
    "./public.routes",
], function (publicControllers, publicRoutes) {
    'use strict';
    angular.module("_public",
        [
            publicControllers,
            publicRoutes
        ]);

    return "_public";
});

