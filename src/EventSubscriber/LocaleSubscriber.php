<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class LocaleSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private string $defaultLocale
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => [
                ['onKernelRequest', 20],
            ],
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();

        if (!$request->hasPreviousSession()) {
            $request->setLocale(
                \Locale::acceptFromHttp($request->headers->get('Accept-Language', $this->defaultLocale))
            );
            return;
        }

        // Try to see if the locale has been set as a _locale routing parameter.
        if ($locale = $request->attributes->get('_locale')) {
            $request->getSession()->set('_locale', $locale);
        } else {
            // If no explicit locale has been set on this request, use one from the session.
            $request->setLocale($request->getSession()->get('_locale', $this->defaultLocale));
        }
    }
}
