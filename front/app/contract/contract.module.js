/**
 *  This file defines the list of controllers and services of contract module
 */
define([
    './contract.controllers',
    './contract.services',
    './contract.filters',
    './contract.routes'
], function (contractControllers, contractServices, contractFilters, contractRoutes) {
    'use strict';

    var moduleName = 'contract';

    angular.module(moduleName, [contractControllers, contractServices, contractFilters, contractRoutes]);

    return moduleName;
});

