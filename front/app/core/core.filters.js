/*global define, angular, list of filters*/
define([
    './filters/displayPrice',
    './filters/abs',
    './filters/name'
], function (displayPriceFilter,abs, name) {
    'use strict';

    var filterModuleName = 'core.filters';

    angular.module(filterModuleName, [])
        .filter('displayPrice', displayPriceFilter)
        .filter('abs', abs)
        .filter('name', name);

    return filterModuleName;
});