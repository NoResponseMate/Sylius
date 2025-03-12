<?php

declare(strict_types=1);

namespace Sylius\Behat\Service\Helper;

use Behat\Mink\Element\NodeElement;

final class LiveComponentHelper implements LiveComponentHelperInterface
{
    public static function waitForComponentReload(NodeElement $element): void
    {
        $component = self::getComponentFromElement($element);

        $component->waitFor(5, function () use ($component) {
            return !$component->hasAttribute('busy');
        });
    }

    private static function getComponentFromElement(NodeElement $element): NodeElement
    {
        if ($element->hasAttribute('data-live-name-value')) {
            return $element;
        }

        $parent = $element->getParent();
        if ('//html' !== $parent->getXpath()) {
            return self::getComponentFromElement($parent);
        }

        throw new \InvalidArgumentException('Cannot find live component element');
    }
}
