/**
 *  This file defines the list of nested modules for core module
 *
 */
define([
    './core.constants',
    './core.services',
    './core.filters',
    './core.config'
], function (coreConstants, coreServices, coreFilters, coreConfig) {
    'use strict';

    var moduleName = 'core';

    angular.module(moduleName, [
        'ui.router',
        'ngLocale',
        'ngResource',
        'restangular',
        'ngDialog',
        'ngMessages',
        'ngFileSaver',
        'restangular',
        'angular-jwt',
        'ui.tinymce',
        'ui-notification',
        'ui.bootstrap',
        'rzModule',
        'ngAnimate',
        'ngMaterial',
        'ngMdIcons',
        'md.data.table',
        'elif',

        // Core submodules
        coreConstants,
        coreServices,
        coreFilters,
        coreConfig
    ]);


    return moduleName;
});

