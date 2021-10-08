<?php
/*
 * Copyright 2020 New Relic Corporation. All rights reserved.
 * SPDX-License-Identifier: Apache-2.0
 */

/* DESCRIPTION
This mocks enough of Symfony 5 for our instrumentation to fire.
*/

namespace Symfony\Component\HttpKernel\Event {

    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpKernel\HttpKernelInterface;

    class KernelEvent {
        private Request $request;

        public function __construct(HttpKernelInterface $kernel, Request $request, ?int $requestType)
        {
            $this->request = $request;
        }

        public function getRequest()
        {
            return $this->request;
        }
    }

    class RequestEvent extends KernelEvent
    {

    }
}

namespace Symfony\Component\HttpFoundation {
  class ParameterBag {
    public $_route;

    public function get($name, $default = null, $deep = false) {
      return isset($this->_route) ? $this->_route : $default;
    }
  }

  class Request {
    public $attributes;
  }

  class Response {}
}

namespace Symfony\Component\HttpKernel\EventListener {
    use Symfony\Component\EventDispatcher\EventSubscriberInterface;
    use Symfony\Component\HttpKernel\Event\RequestEvent;

    class RouterListener implements EventSubscriberInterface
    {
        public function onKernelRequest(RequestEvent $event)
        {

        }

        public static function getSubscribedEvents()
        {
            // TODO: Implement getSubscribedEvents() method.
        }
    }
}

namespace Symfony\Component\HttpKernel {
  use Symfony\Component\HttpFoundation\Response;
  use Symfony\Component\HttpKernel\Event\RequestEvent;
  use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

  final class KernelEvents {
      public const REQUEST = 'kernel.request';
  }


  interface HttpKernelInterface {
    const MASTER_REQUEST = 1;
    const SUB_REQUEST = 2;

    public function handle($request, $type = self::MASTER_REQUEST, $catch = true);
  }

  class HttpKernel implements HttpKernelInterface {
    private EventDispatcherInterface $dispatcher;

    public function __construct(EventDispatcherInterface $dispatcher) {
        $this->dispatcher = $dispatcher;
    }

    public function handle($request, $type = self::MASTER_REQUEST, $catch = true) {
      return $this->handleRaw($request, $type);
    }

    private function handleRaw($request, $type = self::MASTER_REQUEST) {
        // request
        $event = new RequestEvent($this, $request, $type);
        $this->dispatcher->dispatch($event, KernelEvents::REQUEST);

        return new Response;
    }
  }
}


namespace Symfony\Component\EventDispatcher {
    interface EventSubscriberInterface {
        public static function getSubscribedEvents();
    }

    interface EventDispatcherInterface extends \Symfony\Contracts\EventDispatcher\EventDispatcherInterface
    {
        public function addSubscriber(EventSubscriberInterface $subscriber);
    }

    class EventDispatcher implements EventDispatcherInterface
    {
        public function dispatch(object $event, string $eventName = null): object
        {
            $listeners = $this->getListeners($eventName);

            {
                $this->callListeners($listeners, $eventName, $event);
            }
        }

        public function addSubscriber(EventSubscriberInterface $subscriber)
        {

        }
    }
}

namespace Symfony\Contracts\EventDispatcher {
    interface EventDispatcherInterface
    {
        public function dispatch(object $event, string $eventName = null): object;
    }
}
