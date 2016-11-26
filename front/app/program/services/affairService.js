/**
 * @ngdoc service
 * @name program.services:affairService
 * @description
 * This file defines the affairService
 */
define([], function () {
    'use strict';
    affairService.$inject = ["$q", 'Restangular'];
 
    function affairService($q, Restangular) {
        return {
 
            /**
            * @ngdoc method
            * @name postProgramAffair
            * @methodOf program.services:affairService
            * @description
            * Post affair
            * 
            * @param {number} programId Id of program
            * @param {object} data Affair data
            * 
            * @returns {object} Program affair data 
            */
            postProgramAffair: function (programId, data) {
                return Restangular.one('programs', programId).post('affairs', data)
                    .then(function (response) {
                        return response;
                    }, function (fallback) {
                        return $q.reject(fallback);
                    });
            },
 
            /**
            * @ngdoc method
            * @name getProgramAffairs
            * @methodOf program.services:affairService
            * @description
            * Get program affairs
            * 
            * @param {number} programId Id of program
            * @param {string} filter Filter key
            * 
            * @returns {object} Program affairs data 
            */
            getProgramAffairs: function (programId, filter) {
                return Restangular.one('programs', programId).all('affairs').getList(filter)
                    .then(function (response) {
                        return response.data;
                    }, function (fallback) {
                        return $q.reject(fallback);
                    });
            },
 
            /**
            * @ngdoc method
            * @name getProgramAffair
            * @methodOf program.services:affairService
            * @description
            * Get affair by id
            * 
            * @param {number} programId Id of program
            * @param {number} affairId Id of affair
            * 
            * @returns {object} Affair data 
            */
            getProgramAffair: function (programId, affairId) {
                return Restangular.one('programs', programId).one('affairs', affairId).getList()
                    .then(function (response) {
                        return response.data;
                    }, function (fallback) {
                        return $q.reject(fallback);
                    });
            },
 
            /**
            * @ngdoc method
            * @name getAffairUpline
            * @methodOf program.services:affairService
            * @description
            * Get affair upline
            * 
            * @param {number} programId Id of program
            * @param {number} affairId Id of affair
            * 
            * @returns {object} Affair upline data 
            */
            getAffairUpline: function (programId, affairId) {
                return Restangular.one('programs', programId).one('affairs', affairId).getList('upline')
                    .then(function (response) {
                        return response.data;
                    }, function (fallback) {
                        return $q.reject(fallback);
                    });
            },
 
            /**
            * @ngdoc method
            * @name getAffairComissioning
            * @methodOf program.services:affairService
            * @description
            * Get affair comissioning
            * 
            * @param {number} programId Id of program
            * @param {number} affairId Id of affair
            * 
            * @returns {object} Affair comissioning data 
            */
            getAffairComissioning: function (programId, affairId) {
                return Restangular.one('programs', programId).one('affairs', affairId).getList('commissions')
                    .then(function (response) {
                        return response.data;
                    }, function (fallback) {
                        return $q.reject(fallback);
                    });
            },
 
            /**
            * @ngdoc method
            * @name getAffairParticipate
            * @methodOf program.services:affairService
            * @description
            * Get affair participate
            * 
            * @param {number} programId Id of program
            * @param {number} affairId Id of affair
            * 
            * @returns {object} Affair participate data 
            */
            getAffairParticipate: function (programId, affairId) {
                return Restangular.one('programs', programId).one('affairs', affairId).one('participate').get()
                    .then(function (response) {
                        return response.data;
                    }, function (response) {
                        return response;
                    });
            },
 
            /**
            * @ngdoc method
            * @name patchProgramAffair
            * @methodOf program.services:affairService
            * @description
            * Update program affair
            * 
            * @param {number} programId Id of program
            * @param {number} affairId Id of affair
            * @param {object} patch Data to patching
            * 
            * @returns {object} Affair participate data 
            */
            patchProgramAffair: function (programId, affairId, patch) {
                return Restangular.one('programs', programId).one('affairs', affairId).patch(patch)
                    .then(function (response) {
                        return response.data;
                    }, function (fallback) {
                        return $q.reject(fallback);
                    });
            }
        };
    }

    return affairService;
});