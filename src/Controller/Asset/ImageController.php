<?php

declare(strict_types=1);

namespace App\Controller\Asset;

use League\Flysystem\FilesystemInterface;
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
 * @Route("/asset/images/{path}", requirements={"path"=".+"}, name="app_asset_image", methods={"GET"})
 * @Cache(maxage=900, smaxage=900)
 */
final class ImageController extends AbstractController
{
    private FilesystemOperator $defaultStorage;
    private FilesystemOperator $cacheStorage;

    public function __construct(FilesystemOperator $defaultStorage, FilesystemOperator $cacheStorage)
    {
        $this->defaultStorage = $defaultStorage;
        $this->cacheStorage = $cacheStorage;
    }

    public function __invoke(Request $request, string $path, string $secret): Response
    {
        $parameters = $request->query->all();

        if (\count($parameters) > 0) {
            try {
                SignatureFactory::create($secret)->validateRequest($path, $parameters);
            } catch (\Exception $exception) {
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
        } catch (\InvalidArgumentException $exception) {
            throw $this->createNotFoundException();
        }

        return $response;
    }
}
