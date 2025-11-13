<?php

namespace App\Tests\EventSubscriber;

use App\EventSubscriber\AppGlobalsSubscriber;
use App\Repository\UserRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Twig\Environment;

class AppGlobalsSubscriberTest extends TestCase
{
    /**
     * Teste si l'abonné aux événements est correctement inscrit.
     */
    public function testSubscribedEvents(): void
    {
        $events = AppGlobalsSubscriber::getSubscribedEvents();

        // Vérifie que l'abonné écoute bien l'événement KernelEvents::CONTROLLER
        $this->assertArrayHasKey(KernelEvents::CONTROLLER, $events);
        $this->assertEquals('onControllerEvent', $events[KernelEvents::CONTROLLER]);
    }

    /**
     * Teste la méthode onControllerEvent.
     */
    public function testOnControllerEvent(): void
    {
        // Mock du UserRepository
        $userRepository = $this->createMock(UserRepository::class);

        // Simule le retour de la méthode `count` (par exemple 42 membres)
        $userRepository->expects($this->once())
            ->method('count')
            ->with([])
            ->willReturn(42);

        // Mock de Twig\Environment
        $twig = $this->createMock(Environment::class);

        // Vérifie que la méthode addGlobal est appelée avec les bons arguments
        $twig->expects($this->once())
            ->method('addGlobal')
            ->with('nbMembers', 42);

        // Instancie le subscriber
        $subscriber = new AppGlobalsSubscriber($twig, $userRepository);

        // Mock de l'événement ControllerEvent
        $event = $this->createMock(ControllerEvent::class);

        // Appelle la méthode onControllerEvent
        $subscriber->onControllerEvent($event);

        // Si aucune exception ou erreur n'est levée, le test passera
        $this->assertTrue(true, 'onControllerEvent executed successfully.');
    }
}