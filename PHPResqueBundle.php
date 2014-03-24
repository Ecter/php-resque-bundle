<?php

namespace PHPResqueBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class PHPResqueBundle extends Bundle
{
    public function getContainerExtension()
    {
        if (null === $this->extension) {
            $this->extension = new DependencyInjection\PHPResqueExtension();
        }

        return $this->extension;
    }
}