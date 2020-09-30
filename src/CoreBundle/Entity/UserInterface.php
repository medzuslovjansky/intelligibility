<?php

declare(strict_types=1);

namespace Intelligibility\CoreBundle\Entity;

use Symfony\Component\Security\Core\User\UserInterface as BaseUserInterface;

interface UserInterface extends BaseUserInterface, MetaInterface
{
}
