/**
 * @ngdoc controller
 * @name corporate.controllers:CollegeController
 * @description
 * This file defines the home corporate's controller
 */
define([], function () {
    'use strict';

    /*ngInject*/
    function CollegeController(filterFilter, $mdDialog, cooperons, common, collegeService, memberService) {

        var vm = this;

        vm.member = null;
        vm.corporate = null;
        vm.college = null;
        vm.yearlyAttestations = [];

        // functions
        vm.init = init;
        vm.cancelRejoinCollegeRequest = cancelRejoinCollegeRequest;
        vm.resendRejoinCollegeRequest = resendRejoinCollegeRequest;
        vm.transferCollegeDelegate = transferCollegeDelegate;
        vm.acceptCollege = acceptCollege;
        vm.cancelCollege = cancelCollege;
        vm.leaveCollege = leaveCollege;
        vm.confirmCollege = confirmCollege;
        vm.downloadAttestation = collegeService.downloadAttestation;


        /**
         * Note: To use Angular components:
         *  - remove onEvent & init functions
         *  - pass corporate like parameter
         *
         *  => Don't use vm.parent outside of onEvent and init
         */

        common.onEvent(cooperons.events.partyChanged, function (event) {
            init(vm.parent);
        });


        /**
         * @ngdoc method
         * @name init
         * @methodOf party.controllers:partyController
         * @description
         * Retireve parameters
         */
        function init(parent) {
            vm.parent = parent;
            vm.corporate = parent.party.corporate;
            activate();
        }


        /**
         * @ngdoc method
         * @name activate
         * @methodOf party.controllers:partyController
         * @description
         * Initialize data
         */
        function activate() {
            // refresh member college
            return memberService.loadMemberDetails(false, false, true, false).then(function () {
                return collegeService.loadCorporateDetails(vm.corporate).then(function (data) {
                    vm.member = angular.copy(common.getMemberData().member);
                    vm.corporate.administrator = data.administrator;
                    vm.corporate.delegate = data.delegate;
                    vm.college = data.college;
                    vm.corporate.colleges = data.colleges;
                    vm.yearlyAttestations = data.attestations;
                    vm.member.isDelegate = data.isDelegate;
                    vm.member.isAdministrator = data.isAdministrator;
                });
            });
        }


        /**
         * @ngdoc method
         * @name cancelRejoinCollegeRequest
         * @methodOf corporate.controllers:CollegeController
         * @description
         * Cancel request for join college
         *
         * @param {object} college A college data
         */
        function cancelRejoinCollegeRequest() {
            $mdDialog.openConfirm({template: 'college.request.cancel', data: {corporate: corporate}}).then(function () {
                leaveCollege(vm.college);
            });
        }

        /**
         * @ngdoc method
         * @name resendRejoinCollegeRequest
         * @methodOf corporate.controllers:CollegeController
         * @description
         * Resend request for join college
         *
         */
        function resendRejoinCollegeRequest() {
            return collegeService.resendCollegeInvitation(vm.college.id).then(function () {
                toastr.success('La demande de rejoindre le collège de l \'entreprise '
                    + vm.corporate.raisonSocial + 'a été renvoyée', 'Succès');
            });
        }

        /**
         * @ngdoc method
         * @name transferCollegeDelegate
         * @methodOf corporate.controllers:CollegeController
         * @description
         * Change delegate of college
         *
         * @param {object} college A college data
         */
        function transferCollegeDelegate(college) {
            // Select default value for new college delegate
            var colleges = filterFilter(vm.corporate.colleges, {isDelegate: false});
            var newDelegate = colleges[0] || null;

            var data = {corporate: vm.corporate, colleges: colleges, newDelegate: newDelegate};

            $mdDialog.showConfirm({template: 'college.transfer.delegate', data: data}).then(function () {
                collegeService.changeDelegate(newDelegate).then(function () {
                    activate().then(function () {
                        toastr.success('Changement de Délégué effectué avec succès', 'Succès');
                    });

                });
            });
        }

        /**
         * @ngdoc method
         * @name acceptCollege
         * @methodOf corporate.controllers:CollegeController
         * @description
         * Accept College
         *
         * @param {object} college A college data
         */
        function acceptCollege(college) {
            var data = {corporate: corporate, selectedCollege: college};
            $mdDialog.showConfirm({template: 'college.accept', data: data}).then(function () {
                collegeService.acceptCollege(college).then(function () {
                    activate().then(function () {
                        toastr.success('Inscription au Collège Coopérons effectuée avec succès', 'Succès');
                    });
                });
            });
        }

        /**
         * @ngdoc method
         * @name cancelCollege
         * @methodOf corporate.controllers:CollegeController
         * @description
         * Make out college
         *
         * @param {object} college A college data
         */
        function cancelCollege(college) {
            var data = {corporate: corporate, selectedCollege: college, member: vm.member};
            var template = college.status === 'wait_for_delegate' ? 'college.cancel' : 'college.leave';
            $mdDialog.showConfirm({template: template, data: data}).then(function () {
                leaveCollege(college);
            });
        }

        /**
         * @ngdoc method
         * @name leaveCollege
         * @methodOf corporate.controllers:CollegeController
         * @description
         * Leave college
         *
         * @param {object} college A college data
         * @param {string} successMessage A success message
         */
        function leaveCollege(college) {
            collegeService.patchCollege(college).then(function () {
                if (vm.member.college && college.id === vm.member.college.id) {
                    common.broadcast(cooperons.events.refreshParty);
                    toastr.success('Sortie du Collège Coopérons effectuée avec succès', 'Succès');
                } else {
                    activate().then(function () {
                        toastr.success('Sortie du Collège Coopérons effectuée avec succès', 'Succès');
                    });
                }

            });
        }

        /**
         * @ngdoc method
         * @name confirmCollege
         * @methodOf corporate.controllers:CollegeController
         * @description
         * Confirm college
         *
         * @param {object} college A college data
         */
        function confirmCollege(checked) {
            if (checked) {
                collegeService.confirmCollege(college).then(function () {
                    activate().then(function () {
                        toastr.success('Vous êtes confirmé comme salarié de ' + vm.corporate.raisonSocial, 'Succès');
                    });
                });
            } else {
                toastr.info('Vous devez confirmer être toujours salarié de ' + vm.corporate.raisonSocial, 'Attention !');
            }
        }
    }

    return CollegeController;
});
