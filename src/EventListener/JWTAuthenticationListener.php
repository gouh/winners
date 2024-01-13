<?php

namespace App\EventListener;

use App\Response\ApiResponse;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationFailureEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTNotFoundEvent;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;


class JWTAuthenticationListener
{
    public function onAuthenticationSuccess(AuthenticationSuccessEvent $event): void
    {
        $data = $event->getData();
        $response = ApiResponse::createResponse(Response::HTTP_OK, null, $data);
        $event->setData($response);
    }

    public function onAuthenticationFailure(AuthenticationFailureEvent $event): void
    {
        $response = ApiResponse::createResponse(Response::HTTP_UNAUTHORIZED, $event->getException()->getMessage());
        $event->setResponse(new JsonResponse($response, $response['metadata']['statusCode']));
    }

    public function onJwtNotFound(JWTNotFoundEvent $event): void
    {
        $response = ApiResponse::createResponse(Response::HTTP_UNAUTHORIZED, $event->getException()->getMessage());
        $event->setResponse(new JsonResponse($response, $response['metadata']['statusCode']));
    }
}