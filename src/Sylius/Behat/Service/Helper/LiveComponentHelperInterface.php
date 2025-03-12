<?php

declare(strict_types=1);

namespace Sylius\Behat\Service\Helper;

use Behat\Mink\Element\NodeElement;

interface LiveComponentHelperInterface
{
    public static function waitForComponentReload(NodeElement $element);
}
