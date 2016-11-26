/**
 * @ngdoc service
 * @name contract.services:serviceTypeRepository
 * @description
 * This file defines the serviceType service
 */
define([], function () {
    'use strict';
    serviceTypeRepository.$inject = ["$q", 'Restangular','$filter'];
 
    function serviceTypeRepository($q, Restangular, $filter) {


        return {
  
            /**
             * @ngdoc method
             * @name getContractServiceTypes
             * @methodOf contract.services:serviceTypeRepository
             * @description
             * Get contract service types
             *  
             * @param {number} id Id of contract
             * 
             * @returns {object} Contract service types data
             */
            getContractServiceTypes: function (id) {
                return Restangular.one('contracts', id).all('servicetypes').getList()
                        .then(function (response) {
                            return response.data;
                        }, function (fallback) {
                            return $q.reject(fallback);
                        });
            }, 
            
            /**
             * @ngdoc method
             * @name putContractServiceType
             * @methodOf contract.services:serviceTypeRepository
             * @description
             * Update contract service types
             *  
             * @param {number} id Id of contract
             * @param {number} servicetypeId Id of serviceType
             * @param {object} servicetype ServiceType data
             * 
             * @returns {object} Contract service types data
             */
            putContractServiceType: function (id, servicetypeId, servicetype) {
                return Restangular.one('contracts', id).one('servicetypes', servicetypeId).customPUT(servicetype)
                        .then(function (response) {
                            return response.data;
                        }, function (fallback) {
                            return $q.reject(fallback);
                        });

            },
 
            /**
             * @ngdoc method
             * @name postContractServicetype
             * @methodOf contract.services:serviceTypeRepository
             * @description
             * Create contract service types
             *  
             * @param {number} id Id of contract 
             * @param {object} servicetype ServiceType data
             * 
             * @returns {object} Contract service types data
             */
            postContractServicetype: function (id, servicetype) {

                var data = {
                    label: servicetype.label,
                    unitLabel: servicetype.unitLabel,
                    unitDefaultAmount: servicetype.unitDefaultAmount
                };
                return Restangular.one('contracts', id).one('servicetypes').customPOST(data)
                        .then(function (response) {
                            return response.data;
                        }, function (fallback) {
                            return $q.reject(fallback);
                        });
            },
 
            /**
             * @ngdoc method
             * @name deleteContractServicetype
             * @methodOf contract.services:serviceTypeRepository
             * @description
             * Delete contract service types
             *  
             * @param {number} contractId Id of contract 
             * @param {number} serviceTypeId Id of serviceType
             */
            deleteContractServicetype: function (contractId, serviceTypeId) {
                return Restangular.one('contracts', contractId).one('servicetypes', serviceTypeId).remove()
                        .then(function (response) {
                            return response.data;
                        }, function (fallback) {
                            return $q.reject(fallback);
                        });

            }
        };
    }

    return serviceTypeRepository;
});
