<?php

declare(strict_types=1);

namespace App\Controller;

use League\Flysystem\FilesystemOperator;
use League\Glide\Responses\SymfonyResponseFactory;
use League\Glide\ServerFactory;
use League\Glide\Signatures\SignatureFactory;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Cache(maxage=900, smaxage=900)
 */
#[Route(path: '/images/{path}', name: 'app_image', requirements: ['path' => '.+'], methods: ['GET'])]
final class ImageController extends AbstractController
{
    public function __construct(
        private FilesystemOperator $defaultStorage,
        private FilesystemOperator $cacheStorage
    ) {
    }

    public function __invoke(Request $request, string $path, string $secret): Response
    {
        $parameters = $request->query->all();

        if (\count($parameters) > 0) {
            try {
                SignatureFactory::create($secret)->validateRequest($path, $parameters);
            } catch (\Exception) {
                throw $this->createNotFoundException();
            }
        }

        $server = ServerFactory::create([
            'source' => $this->defaultStorage,
            'cache' => $this->cacheStorage,
            'response' => new SymfonyResponseFactory($request),
        ]);

        try {
            $response = $server->getImageResponse($path, $parameters);
        } catch (\InvalidArgumentException) {
            throw $this->createNotFoundException();
        }

        return $response;
    }
}
