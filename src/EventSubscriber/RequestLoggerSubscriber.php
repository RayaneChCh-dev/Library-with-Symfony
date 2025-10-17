<?php
namespace App\EventSubscriber;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\TerminateEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class RequestLoggerSubscriber implements EventSubscriberInterface
{
    private LoggerInterface $logger;
    private ?TokenStorageInterface $tokenStorage;

    public function __construct(LoggerInterface $requestLogger, ?TokenStorageInterface $tokenStorage = null)
    {
        $this->logger = $requestLogger;
        $this->tokenStorage = $tokenStorage;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST   => ['onKernelRequest', 100],
            KernelEvents::TERMINATE => ['onKernelTerminate', 0],
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        $request->attributes->set('_request_start_time', microtime(true));
        // Optionally log minimal info at request start:
        $this->logger->info('request_start', [
            'method' => $request->getMethod(),
            'uri'    => $request->getUri(),
            'ip'     => $request->getClientIp(),
            'route'  => $request->attributes->get('_route'),
        ]);
    }

    public function onKernelTerminate(TerminateEvent $event): void
    {
        $request  = $event->getRequest();
        $response = $event->getResponse();

        $start = $request->attributes->get('_request_start_time', microtime(true));
        $durationMs = round((microtime(true) - $start) * 1000, 2);

        $user = null;
        if ($this->tokenStorage && $this->tokenStorage->getToken()) {
            $tokenUser = $this->tokenStorage->getToken()->getUser();
            $user = is_object($tokenUser) ? method_exists($tokenUser, 'getId') ? $tokenUser->getId() : (string) $tokenUser : $tokenUser;
        }

        $body = $this->getRequestBody($request);
        $body = $this->maskSensitive($body);

        $this->logger->info('request_complete', [
            'method'       => $request->getMethod(),
            'uri'          => $request->getUri(),
            'route'        => $request->attributes->get('_route'),
            'status'       => $response->getStatusCode(),
            'duration_ms'  => $durationMs,
            'ip'           => $request->getClientIp(),
            'user'         => $user,
            'query'        => $request->query->all(),
            'body'         => $body,
            'headers'      => $this->filterHeaders($request->headers->all()),
        ]);
    }

    private function getRequestBody($request): array|string|null
    {
        // Pour JSON API: tenter json_decode, sinon retourner raw string
        $content = $request->getContent();
        if (empty($content)) {
            return null;
        }
        $decoded = json_decode($content, true);
        return $decoded !== null ? $decoded : $content;
    }

    private function maskSensitive($data)
    {
        $sensitiveKeys = ['password','passwd','token','access_token','authorization','credit_card','cc'];
        if (is_array($data)) {
            array_walk_recursive($data, function (&$value, $key) use ($sensitiveKeys) {
                foreach ($sensitiveKeys as $k) {
                    if (stripos((string)$key, $k) !== false) {
                        $value = '***';
                        break;
                    }
                }
            });
            return $data;
        }
        // string: don't log raw auth tokens
        foreach ($sensitiveKeys as $k) {
            if (stripos($data, $k) !== false) {
                return '***';
            }
        }
        return $data;
    }

    private function filterHeaders(array $headers): array
    {
        $deny = ['authorization', 'cookie', 'set-cookie'];
        foreach ($deny as $d) {
            if (isset($headers[$d])) {
                $headers[$d] = ['***'];
            }
        }
        return $headers;
    }
}
