<?php

declare(strict_types=1);

namespace Intelligibility\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class MainController extends AbstractController
{
    public const ERROR = 'intelligibility_error';
    public const INFO = 'intelligibility_info';
    public const SUCCESS = 'intelligibility_success';

    public function addSuccessMessage(string $message): void
    {
        $this->addFlash(self::SUCCESS, $message);
    }

    public function addErrorMessage(string $message): void
    {
        $this->addFlash(self::ERROR, $message);
    }

    public function addInfoMessage(string $message): void
    {
        $this->addFlash(self::INFO, $message);
    }

    public function getRequest(): Request
    {
        return $this->get('request_stack')->getCurrentRequest();
    }
}
