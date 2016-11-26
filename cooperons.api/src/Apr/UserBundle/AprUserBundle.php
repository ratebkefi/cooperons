<?php

namespace Apr\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class AprUserBundle extends Bundle
{
    /**
     * @inheritdoc
     */
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
