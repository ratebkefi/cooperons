/**
 * @ngdoc service
 * @name corporate.services:settingCorporateService
 * @description
 * This file defines the collaboratorRepository
 */
define([], function () {
        'use strict';

        /*ngInject*/
        function settingCorporateService(corporateRepository, collegeRepository) {

            var service = {
                settingCorporate: settingCorporate,
            };

            return service;

            /////////////////////


            /**
             * @ngdoc method
             * @name searchCorporate
             * @methodOf corporate.services:settingCorporateService
             * @description
             * Search corporate by SIREN
             *
             * @param {number} id Corporate id
             *
             * @returns {object} corporate
             */
            function settingCorporate(id) {
                var promise = id > 0 ? corporateRepository.getCorporate(id) : corporateRepository.newCorporate();

                return promise.then(function (data) {
                    var corporate = data.corporate || null;
                    var countries = data.countries || [];
                    var france = null;
                    angular.forEach(countries, function (country) {
                        country.code === 'fr' ? france = country : null;
                    });
                    return {
                        corporate: corporate,
                        countries: countries,
                        france: france
                    }
                });
            }
        }

        return settingCorporateService;
    }
)
;