/*global define, angular, list of filters*/
define([
    './filters/collegeStatus'
], function (collegeStatus) {
    'use strict';

    var filterModuleName = 'corporate.filters';

    angular.module(filterModuleName, [])
        .filter('collegeStatus', collegeStatus);

    return filterModuleName;
});