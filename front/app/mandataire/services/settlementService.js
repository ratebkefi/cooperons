/**
 * @ngdoc service
 * @name mandataire.services:settlementService
 * @description
 * This file defines the settlementService
 */
define([], function () {
    'use strict';
    settlementService.$inject = ["$q", 'Restangular'];
 
    function settlementService($q, Restangular) {
        return {

            baseSettlements: Restangular.all('settlements'),
 
            /**
            * @ngdoc method
            * @name getSettlements
            * @methodOf mandataire.services:settlementService
            * @description
            * Get waiting settlements
            *  
            * @param {string} filter Filter key
            * 
            * @returns {object} Settlements data
            */
            getSettlements: function (filter) {
                return this.baseSettlements.getList(filter)
                    .then(function (response) {
                        return response.data;
                    }, function (fallback) {
                        return $q.reject(fallback);
                    });
            },
 
            /**
            * @ngdoc method
            * @name deleteSettlement
            * @methodOf mandataire.services:settlementService
            * @description
            * Delete settlement
            *  
            * @param {number} id Id of settlements
            *  
            */
            deleteSettlement: function (id) {
                return Restangular.one('settlements', id).remove()
                    .then(function (response) {
                        return response.data;
                    }, function (fallback) {
                        return $q.reject(fallback);
                    });
            }
        };

    }

    return settlementService;
});