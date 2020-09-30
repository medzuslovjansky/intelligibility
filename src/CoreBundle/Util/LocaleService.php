<?php

declare(strict_types=1);

namespace Intelligibility\CoreBundle\Util;

use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;

class LocaleService
{
    public const LOCALE_NAME = '_locale';

    private array $locales;

    private string $defaultLocale;

    private RequestStack $requestStack;

    private ?string $cacheLocale = null;

    public function __construct(string $locales, string $defaultLocale, RequestStack $requestStack)
    {
        $this->locales = explode('|', $locales);
        $this->defaultLocale = $defaultLocale;
        $this->requestStack = $requestStack;
    }

    public function setLocale(Request $request, Response $response, string $locale): void
    {
        if (\in_array($locale, $this->locales, true)) {
            $request->getSession()->set(self::LOCALE_NAME, $locale);
            $request->setLocale($locale);
            $request->cookies->set(self::LOCALE_NAME, $locale);
            $cookie = new Cookie(self::LOCALE_NAME, $locale);
            $response->headers->setCookie($cookie);
        }
    }

    public function getLocales(): array
    {
        return $this->locales;
    }

    public function getCurrentLocale(): string
    {
        if (null === $this->cacheLocale) {
            if (null !== $this->requestStack->getCurrentRequest()) {
                $locale = $this->requestStack->getCurrentRequest()->getSession()->get(self::LOCALE_NAME);
                if (null === $locale) {
                    $locale = $this->requestStack->getCurrentRequest()->cookies->get(self::LOCALE_NAME);
                }
                $this->cacheLocale = $locale;
            }
            if (null === $this->cacheLocale) {
                $this->cacheLocale = $this->defaultLocale;
            }
        }

        return $this->cacheLocale;
    }
}
