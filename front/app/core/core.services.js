/**
 * This file defines the list services of core
 */
define([
    "./services/common",
    "./services/interceptor",
], function (common, interceptor) {
    'use strict';

    var servicesModuleName = "core.services";

    angular.module(servicesModuleName, [])
        .factory('common', common)
        .factory('interceptor', interceptor);

    return servicesModuleName;
});