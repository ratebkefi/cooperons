/*global define, angular, list of filters*/
define([
    './filters/contractStatus',
    './filters/contractLabel',
    './filters/allowContractsActions'
], function (contractStatus, contractLabel, allowContractsActions) {
    'use strict';

    var filterModuleName = 'contract.filters';

    angular.module(filterModuleName, [])
        .filter('contractStatus', contractStatus)
        .filter('contractLabel', contractLabel)
        .filter('allowContractsActions', allowContractsActions);

    return filterModuleName;
});