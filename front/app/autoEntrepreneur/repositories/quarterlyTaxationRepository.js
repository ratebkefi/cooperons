/**
 * @ngdoc service
 * @name autoEntrepreneur.services:quarterlyTaxationRepository
 * @description
 * This file defines the quarterlyTaxation service
 */
define([], function () {
    'use strict';

    /*ngInject*/
    function quarterlyTaxationRepository($q, Restangular) {

        var repository = {
            getQuarterlyTaxations: getQuarterlyTaxations
        };

        return repository;


        /**
         * Get quarterlyTaxation list
         *
         * @param {string} filter
         * @returns object Corporates
         */
        function getQuarterlyTaxations(filter) {
            return Restangular.all('quarterlytaxations').getList(filter).then(
                function (response) {
                    return response.data;
                },
                function (response) {
                    return $q.reject(response);
                }
            );
        }

    }

    return quarterlyTaxationRepository;
});
