/**
 * @ngdoc service
 * @name program.services:participateService
 * @description
 * This file defines the participateService
 */
define([], function () {
    'use strict';
    participateService.$inject = ["$q", 'Restangular'];
 
    function participateService($q, Restangular) {
        return {
 
            /**
            * @ngdoc method
            * @name getProgramOperations
            * @methodOf program.services:participateService
            * @description
            * Get program participate
            *  
            * @param {number} programId Id of program 
            * @param {string} search A key to searching
            * 
            * @returns {object} Program participates
            */
            getProgramParticipates: function (programId, search) {
                return Restangular.one('programs', programId).all('participates').getList({search: search})
                    .then(function (response) {
                        return response.data;
                    }, function (fallback) {
                        return $q.reject(fallback);
                    });
            },
 
            /**
            * @ngdoc method
            * @name postProgramParticipate
            * @methodOf program.services:participateService
            * @description
            * Send invitation
            *  
            * @param {number} programId Id of program 
            * @param {object} participate A participate data
            * 
            * @returns {object} Program participates
            */
            postProgramParticipate: function (programId, participate) {
                return Restangular.one('programs', programId).post('participates', participate)
                    .then(function (response) {
                        return response;
                    }, function (fallback) {
                        return $q.reject(fallback);
                    });
            },
 
            /**
            * @ngdoc method
            * @name getProgramParticipate
            * @methodOf program.services:participateService
            * @description
            * Get program participate
            *  
            * @param {number} programId Id of program 
            * @param {number} participateId Id of participate
            * 
            * @returns {object} Program participate
            */
            getProgramParticipate: function (programId, participateId) {
                return Restangular.one('programs', programId).one('participates', participateId).get()
                    .then(function (response) {
                        return response.data;
                    }, function (fallback) {
                        return $q.reject(fallback);
                    });
            },
 
            /**
            * @ngdoc method
            * @name getParticipatePointsHistory
            * @methodOf program.services:participateService
            * @description
            * Get program points history
            *  
            * @param {number} programId Id of program 
            * @param {number} participateId Id of participate
            * 
            * @returns {object} Program points history
            */
            getParticipatePointsHistory: function (programId, participateId) {
                return Restangular.one('programs', programId).one('participates', participateId).getList('accountpointshistory')
                    .then(function (response) {
                        return response.data;
                    }, function (fallback) {
                        return $q.reject(fallback);
                    });
            },
 
            /**
            * @ngdoc method
            * @name getParticipateFilleuls
            * @methodOf program.services:participateService
            * @description
            * Get program filleuls
            *  
            * @param {number} programId Id of program 
            * @param {number} participateId Id of participate
            * @param {number} filter Filter key
            * 
            * @returns {object} participate filleuls
            */
            getParticipateFilleuls: function (programId, participateId, filter) {
                return Restangular.one('programs', programId).one('participates', participateId).all('filleuls').getList(filter)
                    .then(function (response) {
                        return response.data;
                    }, function (fallback) {
                        return $q.reject(fallback);
                    });
            },
 
            /**
            * @ngdoc method
            * @name getParticipateUpline
            * @methodOf program.services:participateService
            * @description
            * Load participate upline
            *  
            * @param {number} programId Id of program 
            * @param {number} participateId Id of participate
            * @param {array} params A params data
            * 
            * @returns {object} participate upline
            */
            getParticipateUpline: function (programId, participateId, params) {
                return Restangular.one('programs', programId).one('participates', participateId).getList('upline', params)
                    .then(function (response) {
                        return response.data;
                    }, function (response) {
                        return $q.reject(response);
                    });
            },
 
            /**
            * @ngdoc method
            * @name patchProgramParticipate
            * @methodOf program.services:participateService
            * @description
            * Validate operation
            *  
            * @param {number} programId Id of program 
            * @param {number} participateId Id of participate
            * @param {object} patch Data to patching
            * 
            * @returns {object} participate upline
            */
            patchProgramParticipate: function (programId, participateId, patch) {
                return Restangular.one('programs', programId).one('participates', participateId).patch(patch)
                    .then(function (response) {
                        return response;
                    }, function (response) {
                        return $q.reject(response);
                    });
            }
        };
    }

    return participateService;
});