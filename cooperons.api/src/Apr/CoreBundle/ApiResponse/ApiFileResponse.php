<?php
/**
 * This file defines the file api response
 *
 * @category CoreBundle
 * @package ApiResponse
 * @author Fondative <devteam@fondative.com>
 * @copyright 2015-2016 Fondative
 * @version 1.0.0
 * @since File available since Release 1.0.0
 */
namespace Apr\CoreBundle\ApiResponse;

use Apr\CoreBundle\ApiException\ApiException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/**
 * Class ApiFileResponse for API services
 *
 * @category Response
 * @package ApiResponse
 * @author Fondative <devteam@fondative.com>
 * @copyright 2015-2016 Fondative
 * @version 1.0.0
 * @since Class available since Release 1.0.0
 *
 */
class  ApiFileResponse extends BinaryFileResponse
{
    const UPLOADS_FOLDER = 'Uploads';

    const FRAMEWORK_CONTRACT = 1;
    const EASY_DOCUMENT = 2;
    const ATTESTATION = 3;
    const GIFT_ORDER = 4;
    const INVOICE = 5;

    private $folders = array(
        1 => 'CorporateAccords',
        2 => 'EasyDocs',
        3 => 'EmployerAttestations',
        4 => 'GiftOrders',
        5 => 'Invoices'
    );

    /**
     * @param string $fileName
     * @param int $format
     * @param array $type
     * @throws ApiException
     */
    public function __construct($fileName, $format, $type)
    {
        $filePath = __DIR__ . '/../../../../web/' . self::UPLOADS_FOLDER . '/' . $this->folders[$type] . '/' . $fileName . '.' . $format;
        // Check if file exists
        $fs = new Filesystem();
        if (!$fs->exists($filePath)) {
            throw new ApiException(4042);
        }
        parent::__construct($filePath);
        $this->trustXSendfileTypeHeader();
        $this->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_INLINE,
            $fileName,
            iconv('UTF-8', 'ASCII//TRANSLIT', $fileName)
        );
    }
}
