/**
 * @ngdoc service
 * @name mandataire.services:mandataireService
 * @description
 * This file defines the mandataireService
 */
define([], function () {
    'use strict';
    mandataireService.$inject = ["$q", 'Restangular'];
 
    function mandataireService($q, Restangular) {
        return {

            baseMandataire: Restangular.all('mandataires'),
 
            /**
            * @ngdoc method
            * @name getMandataire
            * @methodOf mandataire.services:mandataireService
            * @description
            * Retrieve member's mandataire
            *  
            * @param {number} mandataireId Id of mandataire 
            * 
            * @returns {object} Mandataire data
            */
            getMandataire: function (mandataireId) {
                return Restangular.one('mandataires', mandataireId).get().then(function (response) {
                    return response.data;
                }, function (fallback) {
                    return $q.reject(fallback);
                });
            },
 
            /**
            * @ngdoc method
            * @name getMandataireInfos
            * @methodOf mandataire.services:mandataireService
            * @description
            * Retrieve member's mandataire informations
            *    
            * @returns {object} Mandataire infos data
            */
            getMandataireInfos: function () {
                return Restangular.all('mandataires').one('infos').get()
                    .then(function (response) {
                        return response.data;
                    }, function (fallback) {
                        return $q.reject(fallback);
                    });
            },
 
            /**
            * @ngdoc method
            * @name newMandatairePayment
            * @methodOf mandataire.services:mandataireService
            * @description
            * Get data for new payment
            *  
            * @param {number} id Id of mandataire 
            * @param {number} amount An amount data
            * 
            * @returns {object} Mandataire payment data
            */
            newMandatairePayment: function (id, amount) {
                return Restangular.one('mandataires', id).all('payments').one('new').get({amount: amount})
                    .then(function (response) {
                        return response.data;
                    }, function (fallback) {
                        return $q.reject(fallback);
                    });
            },
 
            /**
            * @ngdoc method
            * @name postMandatairePayment
            * @methodOf mandataire.services:mandataireService
            * @description
            * Post virement
            *  
            * @param {number} id Id of mandataire 
            * @param {number} amount An amount data
            * 
            * @returns {object} Mandataire payment data
            */
            postMandatairePayment: function (id, amount) {
                return Restangular.one('mandataires', id).all('payments').post({amount: amount})
                    .then(function (response) {
                        return response.data;
                    }, function (fallback) {
                        return $q.reject(fallback);
                    });
            },
 
            /**
            * @ngdoc method
            * @name getMandataireOperations
            * @methodOf mandataire.services:mandataireService
            * @description
            * Get mandataires operations
            *  
            * @param {number} mandataireId Id of mandataire  
            * 
            * @returns {object} Mandataire operations data
            */
            getMandataireOperations: function (mandataireId) {
                return Restangular.one('mandataires', mandataireId).getList('operations')
                    .then(function (response) {
                        return response.data;
                    }, function (fallback) {
                        return $q.reject(fallback);
                    });
            },
 
            /**
            * @ngdoc method
            * @name getMandatairePayments
            * @methodOf mandataire.services:mandataireService
            * @description
            * Get mandataires payments
            *  
            * @param {number} mandataireId Id of mandataire
            * @param {string} status Status of payment   
            * 
            * @returns {object} Mandataire operations data
            */
            getMandatairePayments: function (mandataireId, status) {
                return Restangular.one('mandataires', mandataireId).getList('payments', {status: status})
                    .then(function (response) {
                        return response.data;
                    }, function (fallback) {
                        return $q.reject(fallback);
                    });
            },
 
            /**
            * @ngdoc method
            * @name getMandataireSettlements
            * @methodOf mandataire.services:mandataireService
            * @description
            * Get mandataires settlements
            *  
            * @param {number} mandataireId Id of mandataire
            * @param {string} status Status of payment   
            * @param {string} isProgram Is mandataire for program 
            * 
            * @returns {object} Mandataire settlements data
            */
            getMandataireSettlements: function (mandataireId, status, isProgram) {
                return Restangular.one('mandataires', mandataireId).getList('settlements', {
                    status: status,
                    isProgram: isProgram
                })
                    .then(function (response) {
                        return response.data;
                    }, function (fallback) {
                        return $q.reject(fallback);
                    });
            },
 
            /**
            * @ngdoc method
            * @name patchMandataire
            * @methodOf mandataire.services:mandataireService
            * @description
            * Partial updating mandataire
            *  
            * @param {number} id Id of mandataire   
            * @param {object} patch Id of mandataire  
            * 
            * @returns {object} Mandataire contract data
            */
            patchMandataire: function (id, patch) {
                return Restangular.one('mandataires', id).patch(patch)
                    .then(function (response) {
                        return response;
                    }, function (fallback) {
                        return $q.reject(fallback);
                    });
            }, 
            
            /**
            * @ngdoc method
            * @name getMandataireContract
            * @methodOf mandataire.services:mandataireService
            * @description
            * Retrieve mandataire contract
            *  
            * @param {number} mandataireId Id of mandataire   
            * 
            * @returns {object} Mandataire contract data
            */
            getMandataireContract: function (mandataireId) {
                return Restangular.one('mandataires', mandataireId).one('contract').get()
                    .then(function (response) {
                        return response.data;
                    }, function (fallback) {
                        return $q.reject(fallback);
                    });
            },
 
            /**
            * @ngdoc method
            * @name getMandataireOwner
            * @methodOf mandataire.services:mandataireService
            * @description
            * Retrieve mandataire owner
            *  
            * @param {number} mandataireId Id of mandataire   
            * 
            * @returns {object} Mandataire owner data
            */
            getMandataireOwner: function (mandataireId) {
                return Restangular.one('mandataires', mandataireId).one('owner').get()
                    .then(function (response) {
                        return response.data;
                    }, function (fallback) {
                        return $q.reject(fallback);
                    });
            },
 
            /**
            * @ngdoc method
            * @name getMandataireClient
            * @methodOf mandataire.services:mandataireService
            * @description
            * Retrieve mandataire client
            *  
            * @param {number} mandataireId Id of mandataire   
            * 
            * @returns {object} Mandataire client data
            */
            getMandataireClient: function (mandataireId) {
                return Restangular.one('mandataires', mandataireId).one('client').get()
                    .then(function (response) {
                        return response.data;
                    }, function (fallback) {
                        return $q.reject(fallback);
                    });
            },
 
            /**
            * @ngdoc method
            * @name getMandataireAuthorizedParty
            * @methodOf mandataire.services:mandataireService
            * @description
            * Retrieve authorized party client
            *  
            * @param {number} mandataireId Id of mandataire   
            * 
            * @returns {object} Authorized party client data
            */
            getMandataireAuthorizedParty: function (mandataireId) {
                return Restangular.one('mandataires', mandataireId).one('authorizedparty').get()
                    .then(function (response) {
                        return response.data;
                    }, function (fallback) {
                        return $q.reject(fallback);
                    });
            },
 
            /**
            * @ngdoc method
            * @name getMandataireRecords
            * @methodOf mandataire.services:mandataireService
            * @description
            * Retrieve mandataire records
            *  
            * @param {number} mandataireId Id of mandataire   
            * 
            * @returns {object} Mandataire records data
            */
            getMandataireRecords: function (mandataireId) {
                return Restangular.one('mandataires', mandataireId).all('records').getList()
                    .then(function (response) {
                        return response.data;
                    }, function (fallback) {
                        return $q.reject(fallback);
                    });
            },
 
            /**
            * @ngdoc method
            * @name getMandataireInvoice
            * @methodOf mandataire.services:mandataireService
            * @description
            * Retrieve mandataire invoice
            *  
            * @param {number} mandataireId Id of mandataire   
            * 
            * @returns {object} Mandataire invoice data
            */
            getMandataireInvoice: function (mandataireId) {
                return Restangular.one('mandataires', mandataireId).one('invoice').get()
                    .then(function (response) {
                        return response.data;
                    }, function (fallback) {
                        return $q.reject(fallback);
                    });
            }

        };
    }

    return mandataireService;
});
