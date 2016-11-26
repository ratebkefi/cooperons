/**
 * @ngdoc service
 * @name corporate.services:collegeInvitationService
 * @description
 * This file defines the collaboratorRepository
 */
define([], function () {
        'use strict';

        /*ngInject*/
        function collegeInvitationService(corporateRepository, collegeRepository) {

            var service = {
                searchCorporate: searchCorporate,
                resendCollegeInvitation: resendCollegeInvitation,
                createCollege: createCollege
            };

            return service;

            /**
             * @ngdoc method
             * @name searchCorporate
             * @methodOf corporate.services:collegeInvitationService
             * @description
             * Search corporate by SIREN
             *
             * @param {number} siren Corporate siren
             *
             * @returns {object} corporate
             */
            function searchCorporate(siren) {
                return corporateRepository.getCorporates({siren: siren}).then(function (data) {
                    var corporate = data.corporates[0] || null;
                    if (corporate) {
                        return corporateRepository.getCorporateDelegate(corporate.id).then(function (data) {
                            corporate.delegate = data.delegate;
                            return corporate;
                        });
                    } else {
                        return null;
                    }
                });
            }


            /**
             * @ngdoc method
             * @name resendCreateCollegeInvitation
             * @methodOf corporate.controllers:CollegeInvitationController
             * @description
             * Resend invitation to create college
             */
            function resendCollegeInvitation(college) {
                var patch = [{op: 'broadcast', 'path': '/'}];
                return collegeRepository.patchCollege(college.id, patch);
            }

            /**
             * @ngdoc method
             * @name rejoinCollege
             * @methodOf corporate.controllers:CollegeInvitationController
             * @description
             * Confirm rejoin college
             *
             * @param {object} corporate A corporate data
             */
            function createCollege(corporate) {
                var patch = [{op: 'create', path: '/college'}];
                return corporateRepository.patchCorporate(corporate.id, patch);
            }
        }

        return collegeInvitationService;
    }
)
;