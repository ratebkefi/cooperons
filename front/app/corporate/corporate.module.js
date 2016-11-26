/**
 *  This file defines the list of controllers and services of corporate module
 * 
 */
define([ 
    './corporate.controllers',
    './corporate.services',
    './corporate.filters',
    './corporate.routes'
], function (corporateControllers, corporateServices, corporateFilters, corporateRoutes) {
    'use strict';

    var moduleName = 'corporate';

    angular.module(moduleName, [corporateControllers, corporateServices, corporateFilters, corporateRoutes]);

    return moduleName;
});

