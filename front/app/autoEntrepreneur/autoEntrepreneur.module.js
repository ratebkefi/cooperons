/**
 *  This file defines the list of controllers and services of autoEntrepreneur module
 */
define([
    './autoEntrepreneur.routes',
    './autoEntrepreneur.controllers',
    './autoEntrepreneur.services'
], function (autoEntrepreneurRoutes, autoEntrepreneurControllers, autoEntrepreneurServices) {
    'use strict';

    var moduleName = 'autoEntrepreneur';

    angular.module(moduleName, [autoEntrepreneurRoutes, autoEntrepreneurControllers, autoEntrepreneurServices]);

    return moduleName;
});

