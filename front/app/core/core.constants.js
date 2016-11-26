define([
    './constants/config',
    './constants/backRoutes',
    './constants/cooperons'
], function (config, backRoutes, cooperons) {
    'use strict';

    var constantsModuleName = 'core.constants';

    angular.module(constantsModuleName, [])
        .constant('config', config)
        .constant('backRoutes', backRoutes)
        .constant('cooperons', cooperons)
        .constant('toaggstr', toastr)
        .constant('moment', moment);


    return constantsModuleName;
});