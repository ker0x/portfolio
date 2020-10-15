<?php

declare(strict_types=1);

namespace App\Controller\Home;

use App\Service\GithubService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/', name: 'app_home_index')]
final class IndexController extends AbstractController
{
    public function __invoke(GithubService $githubService): Response
    {
        try {
            $repositories = $githubService->getPinnedRepositories();
        } catch (\Exception) {
            $repositories = [];
        }

        $response = $this->render('default/index.html.twig', [
            'repositories' => $repositories,
        ]);
        $response->setSharedMaxAge(3600);

        return $response;
    }
}
