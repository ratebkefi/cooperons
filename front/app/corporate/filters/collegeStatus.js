/**
 * This file defines the filter fo price
 *
 * @category filter
 * @package mandatiare
 * @author Fondative <devteam@fondative.com>
 * @copyright 2015-2016 Fondative
 * @version 0.0.1
 * @since File available since Release 0.0.1
 */

define([], function () {
    'use strict';

    /*ngInject*/
    function collegeStatus() {

        return function (college, memberCollege) {
            var status = '';
            if (college.isDelegate && memberCollege && !memberCollege.isDelegate) {
                status = 'Délégué';
            } else {
                switch (college.status) {
                    case 'wait_for_delegate':
                        status = 'En Attente';
                        break;
                    case 'wait_for_reconfirm':

                        if (college.monthConfirm > 0) {
                            status = 'À confirmer en ' + college.labelMonthConfirm;
                        } else if (college.monthConfirm == 0) {
                            status = 'À confirmer avant la fin du mois';
                        }
                        break;
                    default:
                        status = 'Confirmé';
                }
            }
            return status;
        };
    }

    return collegeStatus;
});