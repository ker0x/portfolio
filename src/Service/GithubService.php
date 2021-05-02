<?php

declare(strict_types=1);

namespace App\Service;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class GithubService
{
    private const LOGIN = 'ker0x';

    public function __construct(
        private CacheInterface $cache,
        private HttpClientInterface $githubClient,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function getPinnedRepositories(): array
    {
        $query = <<<'QUERY'
            query getPinnedRepositories (
                $login: String!
            ) {
                user (
                    login: $login
                ) {
                    pinnedItems(
                        first: 6,
                        types: [REPOSITORY]
                    ) {
                        nodes {
                            ... on Repository {
                                id
                                name
                                description
                                descriptionHTML
                                forkCount
                                stargazerCount
                                url
                                languages(
                                    first: 10
                                ) {
                                    nodes {
                                        id
                                        name
                                        color
                                    }
                                }
                            }
                        }
                    }
                }
            }
            QUERY;

        try {
            return $this->cache->get('github_pinned_repositories', function (ItemInterface $item) use ($query) {
                $item->expiresAfter(60 * 60);

                $response = $this->githubClient->request(Request::METHOD_POST, '/graphql', [
                    'json' => [
                        'query' => $query,
                        'variables' => json_encode(['login' => self::LOGIN], JSON_THROW_ON_ERROR),
                    ],
                ]);

                $json = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);

                return $json['data']['user']['pinnedItems']['nodes'] ?? [];
            });
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage());

            return [];
        }
    }
}
