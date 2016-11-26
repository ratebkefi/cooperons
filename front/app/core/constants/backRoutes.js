/*global angular*/
define([], function () {
    backRoutes = {
        api_doc: '/api/doc',
        faq_corporate: '/public/faq/corporate/__isEasy__/__token__',
        faq_program: '/public/faq/corporate/__isEasy__/__token__',
        faq_member: '/public/faq/member/__token__',
        faq_contract: '/public/faq/contract/',
        cgu_cooperons: '/public/cgu/cooperons/__token__',
        cgv_cooperons: '/public/cgv/cooperons/__token__',
        cgv_program: '/public/cgv/program/__token__',
        landing_contract: '/public/contract/landing',
        landing_college: '/public/college/landing',
        landing_auto_entrepreneur: '/public/auto_entrepreneur/landing',
    };

    return backRoutes;
});