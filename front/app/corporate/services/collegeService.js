/**
 * @ngdoc service
 * @name corporate.services:collegeService
 * @description
 * This file defines the collaboratorRepository
 */
define([], function () {
    'use strict';

    /*ngInject*/
    function collegeService($q, common, corporateRepository, collegeRepository, FileSaver) {

        var service = {
            loadCorporateDetails: loadCorporateDetails,
            leaveCollege: leaveCollege,
            resendCollegeInvitation: resendCollegeInvitation,
            changeDelegate: changeDelegate,
            acceptCollege: acceptCollege,
            confirmCollege: confirmCollege,
            downloadAttestation: downloadAttestation
        };

        return service;

        /**
         * @ngdoc method
         * @name loadCorporateColleges
         * @methodOf corporate.services:collegeService
         * @description
         * Load colleges of corporate
         *
         * @param {object} corporate
         *
         * @returns {array} colleges data
         */
        function loadCorporateDetails(corporate) {
            var promises = [
                corporateRepository.getCorporateAdministrator(corporate.id),
                corporateRepository.getCorporateDelegate(corporate.id),
                corporateRepository.getCorporateColleges(corporate.id),
                corporateRepository.getCorporateYearlyAttestations(corporate.id)
            ];
            return $q.all(promises).then(function (data) {
                var member = angular.copy(common.getMemberData().member);
                var administrator = data[0].administrator;
                var delegate = data[1].delegate;
                var colleges = data[2].colleges;
                var attestations = data[2].attestations;
                var isAdministrator = administrator && administrator.member.id === member.id;
                var isDelegate = delegate && delegate.member.id === member.id;
                var college = null;

                if (member.college && member.college.corporate && corporate.id === member.college.corporate.id) {
                    college = member.college;
                }

                angular.forEach(colleges, function (collegeIterator) {
                    collegeIterator.isAdministrator = administrator && administrator.member.id === collegeIterator.member.id;
                    collegeIterator.isDelegate = delegate && delegate.member.id === collegeIterator.member.id;
                    collegeIterator.canAccept = (isDelegate || (isAdministrator && corporate.accordSigned))
                        && collegeIterator.status == 'wait_for_delegate';
                    collegeIterator.canCancel = (isDelegate || (isAdministrator && corporate.accordSigned))
                        || collegeIterator.member.id == member.id;
                });

                return {
                    administrator: administrator,
                    delegate: delegate,
                    colleges: colleges,
                    attestations: attestations,
                    college: college,
                    isAdministrator: isAdministrator,
                    isDelegate: isDelegate
                }
            });
        }


        /**
         * @ngdoc method
         * @name leaveCollege
         * @methodOf corporate.services:collegeService
         * @description
         * Leave college
         *
         * @param {object} college A college data
         * @param {string} successMessage A success message
         */
        function leaveCollege(college) {
            var patch = [{op: 'leave', 'path': '/'}];
            return collegeRepository.patchCollege(college.id, patch);
        }

        function resendCollegeInvitation(college) {
            var patch = [{op: 'broadcast', 'path': '/'}];
            return collegeRepository.patchCollege(college.id, patch);
        }

        function changeDelegate(newDelegate) {
            var patch = [{op: 'change', 'path': '/delegate'}];
            return collegeRepository.patchCollege(newDelegate.id, patch);
        }

        /**
         * @ngdoc method
         * @name acceptCollege
         * @methodOf corporate.controllers:collegeController
         * @description
         * Accept College
         *
         * @param {object} college A college data
         */
        function acceptCollege(college) {
            var patch = [{op: 'accept', 'path': '/'}];
            return collegeRepository.patchCollege(college.id, patch);
        }

        /**
         * @ngdoc method
         * @name confirmCollege
         * @methodOf corporate.controllers:collegeController
         * @description
         * Confirm college
         *
         * @param {object} college A college data
         */
        function confirmCollege(college) {
            var patch = [{op: 'confirm', 'path': '/'}];
            return collegeRepository.patchCollege(college.id, patch);
        }


        /**
         * @ngdoc method
         * @name downloadAttestation
         * @methodOf corporate.controllers:collegeController
         * @description
         * Download attestation in PDF format
         *
         * @param {object} yearlyAttestation A yearlyAttestation data
         */
        function downloadAttestation(yearlyAttestation) {
            attestationRepository.downloadAttestation(yearlyAttestation.fileName).then(function (data) {
                var blob = new Blob([data], {type: "application/pdf"});
                FileSaver.saveAs(blob, yearlyAttestation.fileName + '.pdf');
            });
        }
    }

    return collegeService;
});