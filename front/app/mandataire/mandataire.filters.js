/*global define, angular, list of services*/
define([  
    "./filters/displayPrice",
    "./filters/abs"
], function (displayPriceFilter,abs) {
    'use strict';
    var filterModuleName = "mandataire.filters"; 
     angular.module(filterModuleName, [])
            .filter("displayPrice", displayPriceFilter)
            .filter("abs", abs);
    return filterModuleName;
});