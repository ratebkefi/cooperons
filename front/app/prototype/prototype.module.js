/**
 * @ngdoc module
 * @name prototype:prototype
 * @description This file inject controllers, services and routes in prototype module
 */

define([
    "./prototype.controllers",
    "./prototype.services",
    "./prototype.routes"

], function (prototypeControllers, prototypeServices, prototypeRoutes) {
    "use strict";

    var moduleName = "prototype";

    angular.module(moduleName, [prototypeControllers, prototypeServices, prototypeRoutes]);

    return moduleName;
});