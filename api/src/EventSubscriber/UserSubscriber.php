<?php

namespace App\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Security;

class UserSubscriber implements EventSubscriberInterface
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        if ('api_users_get_item' !== $request->attributes->get('_route')) {
            return;
        }

        if ('me' !== $request->attributes->get('id')) {
            return;
        }

        $user = $this->security->getUser();

        if (!$user instanceof User) {
            return;
        }

        $request->attributes->set('id', $user->getId());
    }

    public static function getSubscribedEvents()
    {
        return [
            'kernel.request' => ['onKernelRequest', EventPriorities::PRE_READ]
        ];
    }
}
