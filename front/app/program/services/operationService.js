/**
 * @ngdoc service
 * @name program.services:operationService
 * @description
 * This file defines the operationService
 */
define([], function () {
        'use strict';
        operationService.$inject = ['$q', 'Restangular'];
 
        function operationService($q, Restangular) {
            return {
 
                /**
                * @ngdoc method
                * @name getProgramOperations
                * @methodOf program.services:operationService
                * @description
                * Load operations of participate
                *  
                * @param {number} programId Id of program 
                * 
                * @returns {object} Program operations
                */
                getProgramOperations: function (programId) {
                    return Restangular.one('programs', programId).getList('operations')
                        .then(function (response) {
                            return response.data;
                        }, function (response) {
                            return $q.reject(response);
                        });
                },
 
                /**
                * @ngdoc method
                * @name postProgramOperation
                * @methodOf program.services:operationService
                * @description
                * Create operation
                *  
                * @param {number} programId Id of program
                * @param {object} operation An operation data
                * 
                * @returns {object} Program operations
                */
                postProgramOperation: function (programId, operation) {
                    return Restangular.one('programs', programId).post('operations', operation)
                        .then(function (response) {
                            return response;
                        }, function (response) {
                            return $q.reject(response);
                        });
                },
 
                /**
                * @ngdoc method
                * @name putProgramOperation
                * @methodOf program.services:operationService
                * @description
                * Update operation
                *  
                * @param {number} programId Id of program
                * @param {object} operation An operation data
                * 
                * @returns {object} Program operations
                */
                putProgramOperation: function (programId, operation) {
                    return Restangular.one('programs', programId).one('operations', operation.id).customPUT(operation)
                        .then(function (response) {
                            return response;
                        }, function (response) {
                            return $q.reject(response);
                        });
                },
 
                /**
                * @ngdoc method
                * @name deleteProgramOperation
                * @methodOf program.services:operationService
                * @description
                * Delete operation
                *  
                * @param {number} programId Id of program
                * @param {number} operationId Id  of operation
                * 
                * @returns {object} Program operations
                */
                deleteProgramOperation: function (programId, operationId) {
                    return Restangular.one('programs', programId).one('operations', operationId).remove()
                        .then(function (response) {
                            return response;
                        }, function (response) {
                            return $q.reject(response);
                        });
                },
 
                /**
                * @ngdoc method
                * @name getProgramOperationsAsHtml
                * @methodOf program.services:operationService
                * @description
                * Load content html of program operations
                *  
                * @param {number} programId Id of program 
                * 
                * @returns {object} Program operations html contant
                */
                getProgramOperationsAsHtml: function (programId) {
                    return Restangular.one('legal').one('programs', programId).one('operations').get()
                        .then(function (response) {
                            return response;
                        }, function (response) {
                            return $q.reject(response);
                        });
                }
            };
        }

        return operationService;
    }
);
