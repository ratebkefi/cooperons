/**
 * @ngdoc controller
 * @name corporate.controllers:CollegeInvitationController
 * @description
 * This file defines the home corporate's controller
 */
define([], function () {
    'use strict';

    /*ngInject*/
    function CollegeInvitationController($mdDialog, cooperons, common, memberService, collegeInvitationService) {

        var vm = this;

        vm.college = null;
        vm.canRejointCollege = false;
        vm.tokenPlus = '';
        vm.searched = false;
        vm.siren = '';
        vm.corporate = null;

        // Functions
        vm.searchCorporate = searchCorporate;
        vm.resendCreateCollegeInvitation = resendCollegeInvitation;
        vm.cancelCollegeInvitation = cancelCollegeInvitation;
        vm.rejoinCollege = rejoinCollege;
        vm.cancelSearchCorporate = cancelSearchCorporate;
        vm.successCollegeInvitation = activate;


        common.onEvent(cooperons.events.partyChanged, function (event) {
            activate(vm.parent);
        });


        /**
         * @ngdoc method
         * @name searchCorporate
         * @methodOf corporate.controllers:CollegeInvitationController
         * @description
         * Load data
         *
         */
        function activate() {
            // refresh member college
            return memberService.loadMemberDetails(false, false, true, false).then(function () {
                var memberData = common.getMemberData();
                var member = angular.copy(memberData.member);
                vm.tokenPlus = memberData.tokenPlus;
                vm.college = member.college;
                vm.cancelSearchCorporate();
                vm.canRejointCollege = !member.isPreProd && !member.isAutoEntrepreneur && (!vm.college || !vm.college.corporate);
                if (vm.college && !vm.college.corporate) {
                    vm.invitation = vm.college.invitation;
                }
            });
        }

        /**
         * @ngdoc method
         * @name searchCorporate
         * @methodOf corporate.controllers:CollegeInvitationController
         * @description
         * Search corporate by siren
         *
         * @param {object} form Corporate form to be validated
         */
        function searchCorporate(form) {
            if (form.$valid) {
                vm.corporate = null;
                vm.searched = false;
                collegeInvitationService.searchCorporate(vm.siren).then(function (corporate) {
                    vm.corporate = corporate;
                    vm.searched = true;
                });
            }
        }

        /**
         * @ngdoc method
         * @name resendCreateCollegeInvitation
         * @methodOf corporate.controllers:CollegeInvitationController
         * @description
         * Resend invitation to create college
         */
        function resendCollegeInvitation() {
            collegeInvitationService.resendCollegeInvitation(vm.college).then(function () {
                toastr.success('Invitation renvoyée avec succès', 'Succès');
            });
        }

        /**
         * @ngdoc method
         * @name cancelCollegeInvitation
         * @methodOf corporate.controllers:CollegeInvitationController
         * @description
         * Cancel Invitation to cretae college
         */
        function cancelCollegeInvitation() {
            var data = {invitation: vm.invitation};
            $mdDialog.openConfirm({template: 'invitation.college.cancel', data: data}).then(function () {
                collegeService.leaveCollege(vm.college).then(function () {
                    activate().then(function () {
                        toastr.success('Annulation de l\'invitation effectuée avec succès', 'Succès');
                    });
                });
            });
        }

        /**
         * @ngdoc method
         * @name cancelSearch
         * @methodOf corporate.controllers:CollegeInvitationController
         * @description
         * Cancel operation of join college
         */
        function cancelSearchCorporate() {
            vm.searched = false;
            vm.siren = '';
            vm.corporate = null;
        }

        /**
         * @ngdoc method
         * @name rejoinCollege
         * @methodOf corporate.controllers:CollegeInvitationController
         * @description
         * Confirm rejoin college
         *
         */
        function rejoinCollege() {
            collegeInvitationService.createCollege(vm.corporate).then(function () {
                if (vm.corporate.delegate) {
                    toastr.success('Vous avez rejoint le college de l\'entreprise ' + vm.corporate.raisonSocial, 'Succès');
                }
                common.broadcast(cooperons.events.refreshParty);
            });
        }

    }

    return CollegeInvitationController;
});
