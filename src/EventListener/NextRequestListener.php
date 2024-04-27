<?php

namespace App\EventListener;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class NextRequestListener
{
    public function __construct(private Security $security, private UrlGeneratorInterface $urlGenerator) {
    }

    #[AsEventListener(event: KernelEvents::REQUEST)]
    public function onKernelRequest(RequestEvent $event): void
    {
        $session = $event->getRequest()->getSession();
        $user = $this->security->getUser();
        if ($user) {
            if ($user->getStatus() == 'Blocked') {
                if ($session->get('next_request') >= 1) {
                    $route = $this->urlGenerator->generate('app_logout');
                    $event->setResponse(new RedirectResponse($route));
                } else {
                    $current_value = $session->get('next_request', 0);
                    $new_value = $current_value + 1;
                    $session->set('next_request', $new_value);
                }
            }
        }
    }
}
