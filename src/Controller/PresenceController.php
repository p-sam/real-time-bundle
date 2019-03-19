<?php

namespace SP\RealTimeBundle\Controller;

use Ramsey\Uuid\Exception\InvalidUuidStringException;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class PresenceController extends AbstractController
{
    /**
     * @Route("/realtime/presence/{channel}", name="sp_real_time_presence_subscribe", methods={"POST"})
     *
     * @param string $channel
     *
     * @return JsonResponse
     */
    public function subscribe(string $channel): JsonResponse
    {
        return new JsonResponse($this->get('sp_real_time.presence')->subscribe($channel));
    }

    /**
     * @Route("/realtime/presence/{channel}/{uuid}", name="sp_real_time_presence_unsubscribe", methods={"DELETE"})
     *
     * @param string $channel
     *
     * @return Response
     */
    public function unsubscribe(string $channel, string $uuid): Response
    {
        try {
            $parsedUuid = Uuid::fromString($uuid);
        } catch (InvalidUuidStringException $e) {
            throw new BadRequestHttpException('Invalid uuid', $e, Response::HTTP_BAD_REQUEST);
        }

        if (!$this->get('sp_real_time.presence')->unsubscribe($channel, $parsedUuid)) {
            throw new NotFoundHttpException("UUID '${parsedUuid}' not found in channel '${channel}'", null, Response::HTTP_NOT_FOUND);
        }

        return new Response('', 204);
    }

    /**
     * @Route("/realtime/presence/{channel}/{uuid}/beacon_unsubscribe", name="sp_real_time_presence_beacon_unsubscribe", methods={"POST"})
     *
     * @param string $channel
     * @param string $uuid
     *
     * @return Response
     */
    public function unsubscribeBeacon(string $channel, string $uuid): Response
    {
        return $this->unsubscribe($channel, $uuid);
    }
}
