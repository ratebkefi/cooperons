/**
 * return {object} prototypeModel
 */
define([], function () {
        "use strict";
        prototypeModel.$inject = ['$rootScope', '$timeout', '$sce', 'config'];

        function prototypeModel($rootScope, $timeout, $sce, config) {

            var model = {
                generator: {
                    site: 'http://www.json-generator.com/',
                    pattern: {
                        contrats: [
                            '{{repeat(7)}}',
                            {
                                id: '{{index(1)}}',
                                status: 'Non',
                                ownerCollaborator: {
                                    id: '{{index(1)}}',
                                    name: '{{firstName()}} {{surname()}}',
                                    mode: '{{random("Certifié", "Non Certifié")}}',
                                    corporate: {
                                        siren: '{{integer(100000000, 999999999)}}',
                                        address: '{{integer(100, 999)}} {{street()}}, {{city()}}, {{state()}}',
                                        postalCode: '{{integer(10000, 99999)}}'
                                    }
                                },
                                clientCollaborator: {
                                    id: '{{index(50)}}',
                                    name: '{{firstName()}} {{surname()}}',
                                    mode: '{{random("Certifié", "Non Certifié")}}',
                                    corporate: {
                                        siren: '{{integer(100000000, 999999999)}}',
                                        address: '{{integer(100, 999)}} {{street()}}, {{city()}}, {{state()}}',
                                        postalCode: '{{integer(10000, 99999)}}'
                                    }
                                }
                            }
                        ],
                        legalDocuments: [
                            '{{repeat(20)}}',
                            {
                                id: '{{index(1)}}',
                                contratId: '{{integer(1, 7)}}',
                                habilitationId: '{{integer(1, 7)}}',
                                name: '{{lorem(3, "words")}}',
                                signed: '{{bool()}}',
                                terminated: '{{bool()}}',
                                createdDate: '{{date(new Date(2015, 0, 1), new Date(2016, 0, 1), "YYYY-MM-dd")}}',
                                agreeDate: '{{date(new Date(2016, 0, 1), new Date(2016, 3, 1), "YYYY-MM-dd")}}',
                                cancelDate: '{{date(new Date(2016, 0, 1), new Date(), "YYYY-MM-dd")}}',
                                publishDate: '{{date(new Date(2016, 3, 1), new Date(2016, 4, 1), "YYYY-MM-dd")}}',
                                deadlineDate: '{{date(new Date(2016, 6, 1), new Date(2017, 6, 1), "YYYY-MM-dd")}}',
                                cancellationPeriod: '{{integer(0, 3)}}',
                                cancellationPeriodUnit: '{{random("day", "month")}}',
                                status: '{{random("En cours de création", "Aucun gestionnaire")}}',
                                deadlineType: '{{random("undetermined", "duration", "date")}}',
                                deadlineValue: '{{integer(3, 6)}}',
                                deadlineUnit: '{{random("month", "year")}}',
                                file: {
                                    fileName: 'document_contract.pdf'
                                }
                            }
                        ],
                        habilitations: [
                            '{{repeat(7)}}',
                            {
                                id: '{{index(1)}}',
                                active: false,
                                createdDate: '{{date(new Date(2015, 0, 1), new Date(2016, 0, 1), "YYYY-MM-dd")}}',
                                collaborator: {
                                    id: '{{index(100)}}',
                                    name: '{{firstName()}} {{surname()}}',
                                    mode: '{{random("Certifié", "Non Certifié")}}',
                                    corporate: {
                                        siren: '{{integer(100000000, 999999999)}}',
                                        address: '{{integer(100, 999)}} {{street()}}, {{city()}}, {{state()}}',
                                        postalCode: '{{integer(10000, 99999)}}'
                                    }
                                },
                                file: {
                                    fileName: 'document_habilitation.pdf'
                                }
                            }
                        ],
                        collaborator: {
                            id: '{{index(1)}}',
                            name: '{{firstName()}} {{surname()}}',
                            mode: '{{random("Certifié", "Non Certifié")}}',
                            corporate: {
                                siren: '{{integer(100000000, 999999999)}}',
                                address: '{{integer(100, 999)}} {{street()}}, {{city()}}, {{state()}}',
                                postalCode: '{{integer(10000, 99999)}}'
                            }
                        }
                    }
                },
                contrats: [
                    {
                        "id": 1,
                        "status": "Non",
                        "ownerCollaborator": {
                            "id": 1,
                            "name": "Mcfadden Ellison",
                            "mode": "Certifié",
                            "corporate": {
                                "siren": 213344953,
                                "address": "192 Rodney Street, Fannett, New Hampshire",
                                "postalCode": 51169
                            }
                        },
                        "clientCollaborator": {
                            "id": 50,
                            "name": "Marisa Roberson",
                            "mode": "Non Certifié",
                            "corporate": {
                                "siren": 304649453,
                                "address": "411 Huron Street, Thornport, Wyoming",
                                "postalCode": 94586
                            }
                        }
                    },
                    {
                        "id": 2,
                        "status": "Non",
                        "ownerCollaborator": {
                            "id": 2,
                            "name": "Concepcion Gibbs",
                            "mode": "Certifié",
                            "corporate": {
                                "siren": 838470704,
                                "address": "770 Coventry Road, Leroy, Indiana",
                                "postalCode": 17911
                            }
                        },
                        "clientCollaborator": {
                            "id": 51,
                            "name": "Yvette Porter",
                            "mode": "Non Certifié",
                            "corporate": {
                                "siren": 829392429,
                                "address": "407 Schweikerts Walk, Carlos, Alabama",
                                "postalCode": 52029
                            }
                        }
                    },
                    {
                        "id": 3,
                        "status": "Non",
                        "ownerCollaborator": {
                            "id": 3,
                            "name": "Taylor Benson",
                            "mode": "Certifié",
                            "corporate": {
                                "siren": 942488442,
                                "address": "958 Porter Avenue, Yorklyn, Georgia",
                                "postalCode": 48841
                            }
                        },
                        "clientCollaborator": {
                            "id": 52,
                            "name": "Rochelle Martin",
                            "mode": "Certifié",
                            "corporate": {
                                "siren": 938489891,
                                "address": "347 Russell Street, Williston, Montana",
                                "postalCode": 63232
                            }
                        }
                    },
                    {
                        "id": 4,
                        "status": "Non",
                        "ownerCollaborator": {
                            "id": 4,
                            "name": "Mueller Mooney",
                            "mode": "Certifié",
                            "corporate": {
                                "siren": 377604827,
                                "address": "166 Wythe Avenue, Lydia, New Jersey",
                                "postalCode": 33709
                            }
                        },
                        "clientCollaborator": {
                            "id": 53,
                            "name": "Sharlene Case",
                            "mode": "Certifié",
                            "corporate": {
                                "siren": 769400836,
                                "address": "147 Taylor Street, Corinne, Louisiana",
                                "postalCode": 35468
                            }
                        }
                    },
                    {
                        "id": 5,
                        "status": "Non",
                        "ownerCollaborator": {
                            "id": 5,
                            "name": "Williams Byers",
                            "mode": "Certifié",
                            "corporate": {
                                "siren": 643277816,
                                "address": "686 Rutland Road, Yukon, Colorado",
                                "postalCode": 81754
                            }
                        },
                        "clientCollaborator": {
                            "id": 54,
                            "name": "Burris Richard",
                            "mode": "Non Certifié",
                            "corporate": {
                                "siren": 853896369,
                                "address": "555 Etna Street, Westwood, Utah",
                                "postalCode": 28609
                            }
                        }
                    },
                    {
                        "id": 6,
                        "status": "Non",
                        "ownerCollaborator": {
                            "id": 6,
                            "name": "Viola Larsen",
                            "mode": "Certifié",
                            "corporate": {
                                "siren": 326368831,
                                "address": "201 Bliss Terrace, Independence, Delaware",
                                "postalCode": 23479
                            }
                        },
                        "clientCollaborator": {
                            "id": 55,
                            "name": "Tabatha Lamb",
                            "mode": "Non Certifié",
                            "corporate": {
                                "siren": 925953866,
                                "address": "671 Troutman Street, Deputy, Kansas",
                                "postalCode": 74215
                            }
                        }
                    },
                    {
                        "id": 7,
                        "status": "Non",
                        "ownerCollaborator": {
                            "id": 7,
                            "name": "Alston Ballard",
                            "mode": "Non Certifié",
                            "corporate": {
                                "siren": 720461161,
                                "address": "120 Delmonico Place, Dorneyville, Federated States Of Micronesia",
                                "postalCode": 99331
                            }
                        },
                        "clientCollaborator": {
                            "id": 56,
                            "name": "Kristen Bruce",
                            "mode": "Certifié",
                            "corporate": {
                                "siren": 112159372,
                                "address": "718 Division Avenue, Falconaire, Arizona",
                                "postalCode": 76953
                            }
                        }
                    }
                ],
                legalDocuments: [
                    {
                        "id": 1,
                        "contratId": 2,
                        "habilitationId": 4,
                        "name": "tempor laboris in",
                        "signed": false,
                        "terminated": false,
                        "createdDate": "2015-01-05",
                        "agreeDate": "2016-01-21",
                        "cancelDate": "2016-01-08",
                        "publishDate": "2016-04-30",
                        "deadlineDate": "2016-08-29",
                        "cancellationPeriod": 0,
                        "cancellationPeriodUnit": "month",
                        "status": "En cours de création",
                        "deadlineType": "date",
                        "deadlineValue": 3,
                        "deadlineUnit": "year",
                        "file": {
                            "fileName": "document_contract.pdf"
                        }
                    },
                    {
                        "id": 2,
                        "contratId": 6,
                        "habilitationId": 6,
                        "name": "aliquip eiusmod nulla",
                        "signed": true,
                        "terminated": true,
                        "createdDate": "2015-08-19",
                        "agreeDate": "2016-02-02",
                        "cancelDate": "2016-02-13",
                        "publishDate": "2016-04-18",
                        "deadlineDate": "2016-07-03",
                        "cancellationPeriod": 3,
                        "cancellationPeriodUnit": "day",
                        "status": "Aucun gestionnaire",
                        "deadlineType": "undetermined",
                        "deadlineValue": 6,
                        "deadlineUnit": "month",
                        "file": {
                            "fileName": "document_contract.pdf"
                        }
                    },
                    {
                        "id": 3,
                        "contratId": 2,
                        "habilitationId": 6,
                        "name": "aute sit ipsum",
                        "signed": false,
                        "terminated": false,
                        "createdDate": "2015-05-26",
                        "agreeDate": "2016-01-16",
                        "cancelDate": "2016-01-30",
                        "publishDate": "2016-04-09",
                        "deadlineDate": "2017-06-24",
                        "cancellationPeriod": 0,
                        "cancellationPeriodUnit": "month",
                        "status": "Aucun gestionnaire",
                        "deadlineType": "undetermined",
                        "deadlineValue": 4,
                        "deadlineUnit": "month",
                        "file": {
                            "fileName": "document_contract.pdf"
                        }
                    },
                    {
                        "id": 4,
                        "contratId": 5,
                        "habilitationId": 7,
                        "name": "commodo et consectetur",
                        "signed": true,
                        "terminated": false,
                        "createdDate": "2015-11-11",
                        "agreeDate": "2016-01-16",
                        "cancelDate": "2016-01-13",
                        "publishDate": "2016-04-26",
                        "deadlineDate": "2017-02-13",
                        "cancellationPeriod": 1,
                        "cancellationPeriodUnit": "day",
                        "status": "En cours de création",
                        "deadlineType": "date",
                        "deadlineValue": 6,
                        "deadlineUnit": "month",
                        "file": {
                            "fileName": "document_contract.pdf"
                        }
                    },
                    {
                        "id": 5,
                        "contratId": 2,
                        "habilitationId": 4,
                        "name": "occaecat duis minim",
                        "signed": false,
                        "terminated": false,
                        "createdDate": "2015-01-26",
                        "agreeDate": "2016-02-10",
                        "cancelDate": "2016-02-18",
                        "publishDate": "2016-04-06",
                        "deadlineDate": "2017-05-09",
                        "cancellationPeriod": 1,
                        "cancellationPeriodUnit": "month",
                        "status": "Aucun gestionnaire",
                        "deadlineType": "duration",
                        "deadlineValue": 4,
                        "deadlineUnit": "year",
                        "file": {
                            "fileName": "document_contract.pdf"
                        }
                    },
                    {
                        "id": 6,
                        "contratId": 1,
                        "habilitationId": 4,
                        "name": "adipisicing ad exercitation",
                        "signed": false,
                        "terminated": true,
                        "createdDate": "2015-07-29",
                        "agreeDate": "2016-01-31",
                        "cancelDate": "2016-03-14",
                        "publishDate": "2016-04-30",
                        "deadlineDate": "2016-10-17",
                        "cancellationPeriod": 1,
                        "cancellationPeriodUnit": "day",
                        "status": "En cours de création",
                        "deadlineType": "undetermined",
                        "deadlineValue": 3,
                        "deadlineUnit": "month",
                        "file": {
                            "fileName": "document_contract.pdf"
                        }
                    },
                    {
                        "id": 7,
                        "contratId": 1,
                        "habilitationId": 5,
                        "name": "magna duis nulla",
                        "signed": false,
                        "terminated": true,
                        "createdDate": "2015-03-06",
                        "agreeDate": "2016-02-23",
                        "cancelDate": "2016-02-03",
                        "publishDate": "2016-04-29",
                        "deadlineDate": "2017-03-28",
                        "cancellationPeriod": 0,
                        "cancellationPeriodUnit": "month",
                        "status": "En cours de création",
                        "deadlineType": "duration",
                        "deadlineValue": 5,
                        "deadlineUnit": "month",
                        "file": {
                            "fileName": "document_contract.pdf"
                        }
                    },
                    {
                        "id": 8,
                        "contratId": 4,
                        "habilitationId": 3,
                        "name": "tempor nisi nisi",
                        "signed": true,
                        "terminated": false,
                        "createdDate": "2015-04-27",
                        "agreeDate": "2016-03-05",
                        "cancelDate": "2016-01-20",
                        "publishDate": "2016-04-09",
                        "deadlineDate": "2017-04-07",
                        "cancellationPeriod": 0,
                        "cancellationPeriodUnit": "month",
                        "status": "En cours de création",
                        "deadlineType": "duration",
                        "deadlineValue": 4,
                        "deadlineUnit": "month",
                        "file": {
                            "fileName": "document_contract.pdf"
                        }
                    },
                    {
                        "id": 9,
                        "contratId": 3,
                        "habilitationId": 1,
                        "name": "aliquip sit dolore",
                        "signed": false,
                        "terminated": true,
                        "createdDate": "2015-05-14",
                        "agreeDate": "2016-01-04",
                        "cancelDate": "2016-02-09",
                        "publishDate": "2016-04-30",
                        "deadlineDate": "2017-03-05",
                        "cancellationPeriod": 2,
                        "cancellationPeriodUnit": "month",
                        "status": "En cours de création",
                        "deadlineType": "date",
                        "deadlineValue": 6,
                        "deadlineUnit": "month",
                        "file": {
                            "fileName": "document_contract.pdf"
                        }
                    },
                    {
                        "id": 10,
                        "contratId": 5,
                        "habilitationId": 1,
                        "name": "ipsum eiusmod enim",
                        "signed": false,
                        "terminated": true,
                        "createdDate": "2015-06-07",
                        "agreeDate": "2016-01-11",
                        "cancelDate": "2016-02-09",
                        "publishDate": "2016-04-15",
                        "deadlineDate": "2017-01-28",
                        "cancellationPeriod": 1,
                        "cancellationPeriodUnit": "month",
                        "status": "En cours de création",
                        "deadlineType": "date",
                        "deadlineValue": 3,
                        "deadlineUnit": "year",
                        "file": {
                            "fileName": "document_contract.pdf"
                        }
                    },
                    {
                        "id": 11,
                        "contratId": 4,
                        "habilitationId": 5,
                        "name": "consectetur exercitation aute",
                        "signed": true,
                        "terminated": false,
                        "createdDate": "2015-11-27",
                        "agreeDate": "2016-03-29",
                        "cancelDate": "2016-01-10",
                        "publishDate": "2016-04-25",
                        "deadlineDate": "2017-02-24",
                        "cancellationPeriod": 0,
                        "cancellationPeriodUnit": "day",
                        "status": "Aucun gestionnaire",
                        "deadlineType": "duration",
                        "deadlineValue": 4,
                        "deadlineUnit": "month",
                        "file": {
                            "fileName": "document_contract.pdf"
                        }
                    },
                    {
                        "id": 12,
                        "contratId": 6,
                        "habilitationId": 5,
                        "name": "velit consectetur eu",
                        "signed": true,
                        "terminated": false,
                        "createdDate": "2015-01-25",
                        "agreeDate": "2016-02-20",
                        "cancelDate": "2016-01-09",
                        "publishDate": "2016-04-22",
                        "deadlineDate": "2016-10-11",
                        "cancellationPeriod": 0,
                        "cancellationPeriodUnit": "day",
                        "status": "Aucun gestionnaire",
                        "deadlineType": "undetermined",
                        "deadlineValue": 5,
                        "deadlineUnit": "year",
                        "file": {
                            "fileName": "document_contract.pdf"
                        }
                    },
                    {
                        "id": 13,
                        "contratId": 4,
                        "habilitationId": 4,
                        "name": "est qui duis",
                        "signed": false,
                        "terminated": true,
                        "createdDate": "2015-01-18",
                        "agreeDate": "2016-01-09",
                        "cancelDate": "2016-02-14",
                        "publishDate": "2016-04-18",
                        "deadlineDate": "2017-06-21",
                        "cancellationPeriod": 1,
                        "cancellationPeriodUnit": "day",
                        "status": "Aucun gestionnaire",
                        "deadlineType": "duration",
                        "deadlineValue": 6,
                        "deadlineUnit": "month",
                        "file": {
                            "fileName": "document_contract.pdf"
                        }
                    },
                    {
                        "id": 14,
                        "contratId": 7,
                        "habilitationId": 3,
                        "name": "amet consectetur deserunt",
                        "signed": true,
                        "terminated": true,
                        "createdDate": "2015-01-31",
                        "agreeDate": "2016-01-17",
                        "cancelDate": "2016-01-19",
                        "publishDate": "2016-04-19",
                        "deadlineDate": "2017-04-14",
                        "cancellationPeriod": 2,
                        "cancellationPeriodUnit": "day",
                        "status": "Aucun gestionnaire",
                        "deadlineType": "date",
                        "deadlineValue": 6,
                        "deadlineUnit": "year",
                        "file": {
                            "fileName": "document_contract.pdf"
                        }
                    },
                    {
                        "id": 15,
                        "contratId": 5,
                        "habilitationId": 2,
                        "name": "duis sit anim",
                        "signed": true,
                        "terminated": true,
                        "createdDate": "2015-09-24",
                        "agreeDate": "2016-02-24",
                        "cancelDate": "2016-02-12",
                        "publishDate": "2016-04-19",
                        "deadlineDate": "2016-09-15",
                        "cancellationPeriod": 0,
                        "cancellationPeriodUnit": "day",
                        "status": "En cours de création",
                        "deadlineType": "date",
                        "deadlineValue": 5,
                        "deadlineUnit": "month",
                        "file": {
                            "fileName": "document_contract.pdf"
                        }
                    },
                    {
                        "id": 16,
                        "contratId": 1,
                        "habilitationId": 5,
                        "name": "enim dolore duis",
                        "signed": false,
                        "terminated": false,
                        "createdDate": "2015-03-07",
                        "agreeDate": "2016-02-29",
                        "cancelDate": "2016-01-02",
                        "publishDate": "2016-04-15",
                        "deadlineDate": "2017-02-25",
                        "cancellationPeriod": 3,
                        "cancellationPeriodUnit": "day",
                        "status": "Aucun gestionnaire",
                        "deadlineType": "undetermined",
                        "deadlineValue": 5,
                        "deadlineUnit": "year",
                        "file": {
                            "fileName": "document_contract.pdf"
                        }
                    },
                    {
                        "id": 17,
                        "contratId": 1,
                        "habilitationId": 2,
                        "name": "incididunt dolor mollit",
                        "signed": false,
                        "terminated": false,
                        "createdDate": "2015-05-09",
                        "agreeDate": "2016-02-07",
                        "cancelDate": "2016-01-10",
                        "publishDate": "2016-04-24",
                        "deadlineDate": "2016-12-02",
                        "cancellationPeriod": 3,
                        "cancellationPeriodUnit": "month",
                        "status": "En cours de création",
                        "deadlineType": "undetermined",
                        "deadlineValue": 4,
                        "deadlineUnit": "month",
                        "file": {
                            "fileName": "document_contract.pdf"
                        }
                    },
                    {
                        "id": 18,
                        "contratId": 2,
                        "habilitationId": 6,
                        "name": "nostrud labore in",
                        "signed": true,
                        "terminated": true,
                        "createdDate": "2015-03-17",
                        "agreeDate": "2016-02-25",
                        "cancelDate": "2016-01-28",
                        "publishDate": "2016-04-19",
                        "deadlineDate": "2017-04-14",
                        "cancellationPeriod": 3,
                        "cancellationPeriodUnit": "month",
                        "status": "Aucun gestionnaire",
                        "deadlineType": "date",
                        "deadlineValue": 5,
                        "deadlineUnit": "year",
                        "file": {
                            "fileName": "document_contract.pdf"
                        }
                    },
                    {
                        "id": 19,
                        "contratId": 4,
                        "habilitationId": 4,
                        "name": "exercitation laboris ad",
                        "signed": true,
                        "terminated": false,
                        "createdDate": "2015-01-16",
                        "agreeDate": "2016-02-20",
                        "cancelDate": "2016-01-23",
                        "publishDate": "2016-04-04",
                        "deadlineDate": "2016-11-13",
                        "cancellationPeriod": 3,
                        "cancellationPeriodUnit": "day",
                        "status": "En cours de création",
                        "deadlineType": "duration",
                        "deadlineValue": 5,
                        "deadlineUnit": "month",
                        "file": {
                            "fileName": "document_contract.pdf"
                        }
                    },
                    {
                        "id": 20,
                        "contratId": 7,
                        "habilitationId": 2,
                        "name": "duis excepteur eu",
                        "signed": false,
                        "terminated": true,
                        "createdDate": "2015-01-02",
                        "agreeDate": "2016-01-20",
                        "cancelDate": "2016-03-05",
                        "publishDate": "2016-04-30",
                        "deadlineDate": "2017-01-29",
                        "cancellationPeriod": 0,
                        "cancellationPeriodUnit": "month",
                        "status": "Aucun gestionnaire",
                        "deadlineType": "undetermined",
                        "deadlineValue": 5,
                        "deadlineUnit": "year",
                        "file": {
                            "fileName": "document_contract.pdf"
                        }
                    }
                ],
                habilitations: [
                    {
                        "id": 1,
                        "active": true,
                        "createdDate": "2016-01-25",
                        "collaborator": {
                            "id": 100,
                            "name": "Nunez Decker",
                            "mode": "Non Certifié",
                            "corporate": {
                                "siren": 492447385,
                                "address": "544 McDonald Avenue, Mammoth, Louisiana",
                                "postalCode": 39921
                            }
                        },
                        "file": {
                            "fileName": "document_habilitation.pdf"
                        }
                    },
                    {
                        "id": 2,
                        "active": false,
                        "createdDate": "2015-07-30",
                        "collaborator": {
                            "id": 101,
                            "name": "Laurie Kirkland",
                            "mode": "Certifié",
                            "corporate": {
                                "siren": 191497119,
                                "address": "684 Imlay Street, Boonville, New Jersey",
                                "postalCode": 46244
                            }
                        },
                        "file": {
                            "fileName": "document_habilitation.pdf"
                        }
                    },
                    {
                        "id": 3,
                        "active": false,
                        "createdDate": "2015-02-27",
                        "collaborator": {
                            "id": 102,
                            "name": "Burks Pena",
                            "mode": "Non Certifié",
                            "corporate": {
                                "siren": 869291120,
                                "address": "225 Erskine Loop, Trinway, Kentucky",
                                "postalCode": 41386
                            }
                        },
                        "file": {
                            "fileName": "document_habilitation.pdf"
                        }
                    },
                    {
                        "id": 4,
                        "active": false,
                        "createdDate": "2015-11-23",
                        "collaborator": {
                            "id": 103,
                            "name": "Gibson Bass",
                            "mode": "Certifié",
                            "corporate": {
                                "siren": 320833584,
                                "address": "860 Hendrickson Street, Healy, Indiana",
                                "postalCode": 80426
                            }
                        },
                        "file": {
                            "fileName": "document_habilitation.pdf"
                        }
                    },
                    {
                        "id": 5,
                        "active": false,
                        "createdDate": "2015-01-13",
                        "collaborator": {
                            "id": 104,
                            "name": "Castro Duffy",
                            "mode": "Non Certifié",
                            "corporate": {
                                "siren": 582728082,
                                "address": "181 Covert Street, Rockhill, Michigan",
                                "postalCode": 47631
                            }
                        },
                        "file": {
                            "fileName": "document_habilitation.pdf"
                        }
                    },
                    {
                        "id": 6,
                        "active": false,
                        "createdDate": "2015-04-09",
                        "collaborator": {
                            "id": 105,
                            "name": "Duffy Mays",
                            "mode": "Certifié",
                            "corporate": {
                                "siren": 510354748,
                                "address": "659 Ashland Place, Lindcove, Hawaii",
                                "postalCode": 68980
                            }
                        },
                        "file": {
                            "fileName": "document_habilitation.pdf"
                        }
                    },
                    {
                        "id": 7,
                        "active": false,
                        "createdDate": "2015-07-22",
                        "collaborator": {
                            "id": 106,
                            "name": "Acosta Gross",
                            "mode": "Certifié",
                            "corporate": {
                                "siren": 250040693,
                                "address": "183 Brevoort Place, Weeksville, Federated States Of Micronesia",
                                "postalCode": 35249
                            }
                        },
                        "file": {
                            "fileName": "document_habilitation.pdf"
                        }
                    }
                ],
                collaborator: {
                    "id": 1,
                    "name": "Maura Cannon",
                    "mode": "Non Certifié",
                    "corporate": {
                        "siren": 613173387,
                        "address": "148 Roder Avenue, Chumuckla, Louisiana",
                        "postalCode": 14427
                    }
                },
                lastContractId: 7,
                lastLegalDocumentId: 20,
                lastHabilitationId: 7
            };

            return {
                getContrats: function () {
                    return $timeout(function () {
                        return {
                            code: 200,
                            status: 'success',
                            message: '',
                            data: {
                                contrats: angular.copy(model.contrats)
                            }
                        };
                    }, 1000);
                },

                getContrat: function (contratId) {
                    return $timeout(function () {
                        var contrat;
                        angular.forEach(model.contrats, function (contratIterator) {
                            if (contratIterator.id == contratId) {
                                contrat = contratIterator;
                            }
                        });
                        return {
                            code: 200,
                            status: 'success',
                            message: '',
                            data: {
                                contrat: angular.copy(contrat)
                            }
                        };
                    }, 1000);
                },

                getContratLegalDocuments: function (contratId) {
                    return $timeout(function () {
                        var legalDocuments = [];
                        angular.forEach(model.legalDocuments, function (legalDocumentIterator) {
                            if (legalDocumentIterator.contratId == contratId) {
                                legalDocuments.push(legalDocumentIterator);
                            }
                        });

                        return {
                            code: 200,
                            status: 'success',
                            message: '',
                            data: {
                                legalDocuments: angular.copy(legalDocuments)
                            }
                        };
                    }, 1000);

                },

                postContratLegalDocument: function (contratId, legalDocument) {
                    return $timeout(function () {
                        legalDocument.id = ++model.lastLegalDocumentId;
                        legalDocument.file = {fileName: 'document_contract.pdf'};
                        model.legalDocuments.push(legalDocument);

                        return {
                            code: 201,
                            status: 'success',
                            message: '',
                            data: {
                                legalDocuments: angular.copy(legalDocument)
                            }
                        };
                    }, 1000);

                },

                putContratLegalDocument: function (contratId, idLegalDocument, legalDocument) {
                    return $timeout(function () {
                        legalDocument.file = {fileName: 'document_contract.pdf'};
                        var i = 0;
                        angular.forEach(model.legalDocuments, function (legalDocumentIterator) {
                            if (legalDocumentIterator.id == idLegalDocument && legalDocumentIterator.contratId == contratId) {
                                model.legalDocuments[i] = legalDocument;
                            }
                            i++;
                        });
                        return {
                            code: 204,
                            status: 'success',
                            message: '',
                            data: {}
                        };
                    }, 1000);

                },

                patchContratLegalDocument: function (contratId, idLegalDocument, patch) {
                    return $timeout(function () {
                        return {
                            code: 204,
                            status: 'success',
                            message: '',
                            data: {}
                        };
                    }, 1000);

                },

                deleteContratLegalDocument: function (contratId, idLegalDocument) {
                    return $timeout(function () {
                        var i = 0;
                        angular.forEach(model.legalDocuments, function (legalDocumentIterator) {
                            if (legalDocumentIterator.id == idLegalDocument && legalDocumentIterator.contratId == contratId) {
                                model.legalDocuments.splice(i, 1);
                            }
                            i++;

                        });

                        return {
                            code: 204,
                            status: 'success',
                            message: '',
                            data: {}
                        };
                    }, 1000);

                },

                getLegalDocumentFile: function (contratId, idLegalDocument) {
                    return $timeout(function () {
                        var legalDocument;
                        angular.forEach(model.legalDocuments, function (legalDocumentIterator) {
                            if (legalDocumentIterator.id == idLegalDocument && legalDocumentIterator.contratId == contratId) {
                                legalDocument = legalDocumentIterator;
                            }
                        });

                        var file = {
                            name: legalDocument.file.fileName,
                            content: $sce.trustAsResourceUrl(config.uploadedFilesUrl + '/LegalDocuments/' + legalDocument.file.fileName)
                        };

                        return {
                            code: 200,
                            status: 'success',
                            message: '',
                            data: {
                                file: file
                            }
                        };
                    }, 1000);
                },

                getHabilitations: function () {
                    return $timeout(function () {
                        return {
                            code: 200,
                            status: 'success',
                            message: '',
                            data: {
                                habilitations: angular.copy(model.habilitations)
                            }
                        };
                    }, 1000);
                },

                getHabilitation: function (habilitationId) {
                    return $timeout(function () {
                        var habilitation;
                        angular.forEach(model.habilitations, function (habilitationIterator) {
                            if (habilitationIterator.id == habilitationId) {
                                habilitation = habilitationIterator;
                            }
                        });
                        return {
                            code: 200,
                            status: 'success',
                            message: '',
                            data: {
                                habilitation: angular.copy(habilitation)
                            }
                        };
                    }, 1000);
                },

                getHabilitationFile: function (habilitationId) {
                    return $timeout(function () {
                        var habilitation;

                        angular.forEach(model.habilitations, function (habilitationIterator) {
                            if (habilitationIterator.id == habilitationId) {
                                habilitation = habilitationIterator;
                            }
                        });

                        var file = {
                            name: habilitation.file.fileName,
                            content: $sce.trustAsResourceUrl(config.uploadedFilesUrl + '/habilitationDocuments/' + habilitation.file.fileName)
                        };

                        return {
                            code: 200,
                            status: 'success',
                            message: '',
                            data: {
                                file: file
                            }
                        };
                    }, 1000);
                },

                getHabilitationLegalDocuments: function (habilitationId) {
                    return $timeout(function () {
                        var legalDocuments = [];
                        angular.forEach(model.legalDocuments, function (legalDocumentIterator) {
                            if (legalDocumentIterator.habilitationId == habilitationId) {
                                legalDocuments.push(legalDocumentIterator);
                            }
                        });

                        return {
                            code: 200,
                            status: 'success',
                            message: '',
                            data: {
                                legalDocuments: angular.copy(legalDocuments)
                            }
                        };
                    }, 1000);

                },

                postHabilitation: function (habilitation) {
                    return $timeout(function () {
                        habilitation.id = ++model.lastHabilitationId;
                        habilitation.file = {fileName: 'document_habilitation.pdf'};
                        habilitation.createdDate = new Date();
                        habilitation.createdDate = new Date();
                        habilitation.createdDate = habilitation.createdDate.toISOString();
                        habilitation.collaborator = angular.copy(model.collaborator);
                        habilitation.active = true;
                        angular.forEach(model.habilitations, function (habilitationIterator) {
                            habilitationIterator.active = false;
                        });
                        model.habilitations.push(habilitation);

                        return {
                            code: 201,
                            status: 'success',
                            message: '',
                            data: {
                                habilitation: angular.copy(habilitation)
                            }
                        };
                    }, 1000);

                }


            }
        }

        return prototypeModel;
    }
);