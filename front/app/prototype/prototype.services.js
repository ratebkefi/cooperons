/**
 * @ngdoc module
 * @name prototype:prototype.services
 * @description This file for declaring services of prototype module
 */

define([
    './repositories/contratPrototype',
    './repositories/habilitationPrototype',
    './model/prototypeModel'
], function (contratPrototype, habilitationPrototype, prototypeModel) {
    'use strict';

    var servicesModuleName = 'prototype.services';

    angular.module(servicesModuleName, [])
        .factory('contratPrototype', contratPrototype)
        .factory('habilitationPrototype', habilitationPrototype)
        .factory('prototypeModel', prototypeModel);

    return servicesModuleName;
});