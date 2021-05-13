<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\GithubService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/github', name: 'app_github')]
final class GithubController extends AbstractController
{
    public function __invoke(GithubService $githubService): Response
    {
        $response = $this->render('default/github.html.twig', [
            'repositories' => $githubService->getPinnedRepositories(),
        ]);
        $response->setSharedMaxAge(3600);

        return $response;
    }
}
