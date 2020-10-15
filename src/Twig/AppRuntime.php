<?php

declare(strict_types=1);

namespace App\Twig;

use League\Glide\Signatures\SignatureFactory;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Extension\RuntimeExtensionInterface;

final class AppRuntime implements RuntimeExtensionInterface
{
    private UrlGeneratorInterface $urlGenerator;
    private string $secret;

    public function __construct(UrlGeneratorInterface $urlGenerator, string $secret)
    {
        $this->urlGenerator = $urlGenerator;
        $this->secret = $secret;
    }

    public function generateAssetUrl(string $path, array $parameters = [], int $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH): string
    {
        $parameters['fm'] = 'pjpg';
        $parameters['s'] = SignatureFactory::create($this->secret)->generateSignature($path, $parameters);
        $parameters['path'] = ltrim($path, '/');

        return $this->urlGenerator->generate('app_asset_image', $parameters, $referenceType);
    }
}
