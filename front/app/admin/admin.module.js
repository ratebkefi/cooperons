/**
 * This file defines the list of controllers, directives and services of admin module
 * 
 * @param {controller} adminControllers
 * @param {route} adminRoutes
 * @param {directive} adminDirectives
 * @returns {String}
 */
define([
    "./admin.controllers",
    "./admin.routes",
    "./admin.directives"

], function (adminControllers, adminRoutes, adminDirectives) {
    'use strict';

    angular.module("admin", [adminControllers, adminRoutes, adminDirectives]);

    return "admin";
});

