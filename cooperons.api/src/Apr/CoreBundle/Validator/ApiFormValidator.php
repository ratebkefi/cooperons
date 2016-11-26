<?php

/**
 * This file defines the data validator for REST API
 *
 * @category CoreBundle
 * @package Validator
 * @author Fondative <devteam@fondative.com>
 * @copyright 2015-2016 Fondative
 * @version 1.0.0
 * @since File available since Release 1.0.0
 */

namespace Apr\CoreBundle\Validator;

use Apr\CoreBundle\ApiException\ApiException;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormFactory;

/**
 * Class ApiFormValidator for API services
 *
 * @category CoreBundle
 * @package Validator
 * @author Fondative <devteam@fondative.com>
 * @copyright 2015-2016 Fondative
 * @version 1.0.0
 * @since Class available since Release 1.0.0
 *
 */
class ApiFormValidator
{

    const EXCEPTION_MESSAGE = 'No validation is processed. Look to execute  validateData function';

    private $formFactory;
    private $process = false;
    private $form;
    private $isValid;
    private $errors = array();
    private $formType;

    public function __construct(FormFactory $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    /**
     * Validate entities data.
     *
     * @param $formType
     * @param $data
     * @return bool
     */
    public function validateData($formType, $data, $object = null)
    {
        $this->formType = $formType;
        $this->form = $this->formFactory->create($formType, $object, array('csrf_protection' => false));
        $data = array_intersect_key($data, $this->form->all());
        $this->form->submit($data);

        $this->isValid = $this->form->isValid();
        $this->process = true;

        if (!$this->isValid) {
            $this->errors = $this->isValid ? array() : $this->loadErrors($this->form);
        }

        return $this->isValid;
    }

    /**
     * Get error messages from form.
     *
     * @param $form
     * @return array
     */
    private function loadErrors($form)
    {
        $errors = array();
        $children = $form->getIterator();
        /** @var Form $child */
        foreach ($children as $child) {
            if (!$child->isValid()) {
                $errors[$child->getName()] = $this->loadErrors($child);
            }
        }
        foreach ($form->getErrors() as $key => $error) {
            $errors[] = $error->getMessage();
        }
        return $errors;
    }

    /**
     * Get form
     *
     * @return Form
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * Return form data
     *
     * @return object
     * @throws \Exception validateData function not executed
     */
    public function getData()
    {
        if (!$this->process) {
            throw new \Exception(self::EXCEPTION_MESSAGE);
        }
        return $this->form->getData();
    }

    /**
     * Return error messages.
     *
     * @return array of errors
     * @throws \Exception validateData function not executed
     */
    public function getErrors()
    {
        if (!$this->process) {
            throw new \Exception(self::EXCEPTION_MESSAGE);
        }
        return $this->errors;
    }

    /**
     * Return true if data are valid, false else.
     * @return boolean
     * @throws \Exception validateData function not executed
     */
    public function isValid()
    {
        if (!$this->process) {
            throw new \Exception(self::EXCEPTION_MESSAGE);
        }
        return $this->isValid();
    }

}
