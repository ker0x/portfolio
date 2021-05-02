<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\GithubService;
use App\Service\TwitterService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/', name: 'app_home')]
final class HomeController extends AbstractController
{
    public function __invoke(
        TwitterService $twitterService,
    ): Response {
        $response = $this->render('default/index.html.twig', [
            'tweets' => $twitterService->getTimeline(),
        ]);
        $response->setSharedMaxAge(3600);

        return $response;
    }
}
