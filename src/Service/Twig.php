<?php

# src/Service/Twig.php

namespace App\Service;

use Symfony\Component\HttpKernel\KernelInterface;

class Twig extends \Twig_Environment {

    public function __construct(KernelInterface $kernel) {
        $loader = new \Twig_Loader_Filesystem($kernel->getProjectDir());

        parent::__construct($loader);
    }
}