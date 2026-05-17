<?php

declare(strict_types=1);

namespace Database\Seeders;

use DateTimeImmutable;
use PDO;
use Throwable;

final class BlogSeeder
{
    private DateTimeImmutable $seededAt;

    public function __construct(
        private readonly PDO $pdo,
    ) {
    }

    public function seed(): void
    {
        $this->seededAt = new DateTimeImmutable();

        $this->pdo->beginTransaction();

        try {
            $this->clearTables();
            $categoryIds = $this->seedCategories();
            $this->seedArticles($categoryIds);
            $this->pdo->commit();
        } catch (Throwable $exception) {
            $this->pdo->rollBack();

            throw $exception;
        }
    }

    private function clearTables(): void
    {
        $this->pdo->exec('DELETE FROM article_category');
        $this->pdo->exec('DELETE FROM articles');
        $this->pdo->exec('DELETE FROM categories');
    }

    private function seedCategories(): array
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO categories (slug, title, description, created_at, updated_at)
             VALUES (:slug, :title, :description, :created_at, :updated_at)',
        );
        $categoryIds = [];

        foreach ($this->getCategories() as $category) {
            $statement->execute([
                'slug' => $category['slug'],
                'title' => $category['title'],
                'description' => $category['description'],
                'created_at' => $this->timestamp(),
                'updated_at' => $this->timestamp(),
            ]);
            $categoryIds[$category['slug']] = (int) $this->pdo->lastInsertId();
        }

        return $categoryIds;
    }

    private function seedArticles(array $categoryIds): void
    {
        $articleStatement = $this->pdo->prepare(
            'INSERT INTO articles
                (slug, image, title, description, body, views_count, published_at, created_at, updated_at)
             VALUES
                (:slug, :image, :title, :description, :body, :views_count, :published_at, :created_at, :updated_at)',
        );
        $relationStatement = $this->pdo->prepare(
            'INSERT INTO article_category (article_id, category_id)
             VALUES (:article_id, :category_id)',
        );

        foreach ($this->getArticles() as $index => $article) {
            $articleStatement->execute([
                'slug' => $article['slug'],
                'image' => $article['image'],
                'title' => $article['title'],
                'description' => $article['description'],
                'body' => $article['body'],
                'views_count' => $article['views_count'],
                'published_at' => $this->publishedAt($index),
                'created_at' => $this->timestamp(),
                'updated_at' => $this->timestamp(),
            ]);
            $articleId = (int) $this->pdo->lastInsertId();

            foreach ($article['categories'] as $categorySlug) {
                $relationStatement->execute([
                    'article_id' => $articleId,
                    'category_id' => $categoryIds[$categorySlug],
                ]);
            }
        }
    }

    private function timestamp(): string
    {
        return $this->seededAt->format('Y-m-d H:i:s');
    }

    private function publishedAt(int $index): string
    {
        return $this->seededAt
            ->modify(sprintf('-%d days', $index))
            ->format('Y-m-d H:i:s');
    }

    private function getCategories(): array
    {
        return [
            [
                'slug' => 'php-development',
                'title' => 'PHP Development',
                'description' => 'Native PHP patterns, strict typing, PDO, and maintainable server-side code.',
            ],
            [
                'slug' => 'database-design',
                'title' => 'Database Design',
                'description' => 'Schema design, SQL performance, indexing, and safe data access with MySQL.',
            ],
            [
                'slug' => 'frontend-craft',
                'title' => 'Frontend Craft',
                'description' => 'Practical interface details, responsive layouts, templates, and asset workflows.',
            ],
            [
                'slug' => 'devops-notes',
                'title' => 'DevOps Notes',
                'description' => 'Docker, nginx, repeatable local environments, and deployment-minded habits.',
            ],
            [
                'slug' => 'architecture',
                'title' => 'Architecture',
                'description' => 'Small application structure, services, repositories, ' .
                    'and clear responsibility boundaries.',
            ],
        ];
    }

    private function getArticles(): array
    {
        return [
            $this->article(
                'strict-types-in-native-php',
                'Strict Types in Native PHP',
                'How strict typing keeps small PHP applications predictable.',
                'php-card.svg',
                124,
                ['php-development', 'architecture'],
            ),
            $this->article(
                'pdo-prepared-statements',
                'PDO Prepared Statements',
                'A practical look at safe database queries without a framework.',
                'database-card.svg',
                216,
                ['php-development', 'database-design'],
            ),
            $this->article(
                'smarty-layout-basics',
                'Smarty Layout Basics',
                'Using layouts and partials to keep templates focused.',
                'frontend-card.svg',
                88,
                ['frontend-craft', 'php-development'],
            ),
            $this->article(
                'docker-compose-for-php',
                'Docker Compose for PHP',
                'A compact PHP, nginx, and MySQL development setup.',
                'devops-card.svg',
                301,
                ['devops-notes', 'php-development'],
            ),
            $this->article(
                'thin-controllers',
                'Thin Controllers',
                'Keeping HTTP classes small by moving page logic into services.',
                'architecture-card.svg',
                177,
                ['architecture', 'php-development'],
            ),
            $this->article(
                'category-pagination',
                'Category Pagination',
                'Designing paginated lists that are easy to scan and safe to query.',
                'frontend-card.svg',
                142,
                ['frontend-craft', 'database-design'],
            ),
            $this->article(
                'mysql-indexes-for-blog',
                'MySQL Indexes for a Blog',
                'Indexes that help category pages and article sorting stay responsive.',
                'database-card.svg',
                364,
                ['database-design', 'architecture'],
            ),
            $this->article(
                'nginx-front-controller',
                'nginx Front Controller',
                'Routing every public PHP request through one entry point.',
                'devops-card.svg',
                119,
                ['devops-notes', 'architecture'],
            ),
            $this->article(
                'service-layer-scenarios',
                'Service Layer Scenarios',
                'How page services coordinate repositories and DTOs.',
                'architecture-card.svg',
                233,
                ['architecture', 'php-development'],
            ),
            $this->article(
                'responsive-blog-grid',
                'Responsive Blog Grid',
                'Building the three-column article grid from the blog layout.',
                'frontend-card.svg',
                97,
                ['frontend-craft'],
            ),
            $this->article(
                'many-to-many-categories',
                'Many-to-Many Categories',
                'Why a pivot table fits articles that belong to several categories.',
                'database-card.svg',
                278,
                ['database-design', 'architecture'],
            ),
            $this->article(
                'repeatable-database-seeding',
                'Repeatable Database Seeding',
                'Using predictable seed data to make manual testing faster.',
                'devops-card.svg',
                205,
                ['devops-notes', 'database-design'],
            ),
            $this->article(
                'dto-template-data',
                'DTO Template Data',
                'Passing structured page data into Smarty templates.',
                'php-card.svg',
                156,
                ['php-development', 'frontend-craft'],
            ),
            $this->article(
                'safe-sort-parameters',
                'Safe Sort Parameters',
                'Whitelisting sort values before they reach an ORDER BY clause.',
                'database-card.svg',
                412,
                ['database-design', 'php-development'],
            ),
            $this->article(
                'local-node-assets',
                'Local Node Assets',
                'Compiling SCSS in Docker without installing Node on the host.',
                'devops-card.svg',
                134,
                ['devops-notes', 'frontend-craft'],
            ),
            $this->article(
                'blog-card-anatomy',
                'Blog Card Anatomy',
                'Image ratios, compact text, and quiet links for article previews.',
                'frontend-card.svg',
                91,
                ['frontend-craft'],
            ),
            $this->article(
                'repository-boundaries',
                'Repository Boundaries',
                'Keeping SQL in one layer and page decisions in another.',
                'architecture-card.svg',
                245,
                ['architecture', 'database-design'],
            ),
            $this->article(
                'view-count-updates',
                'View Count Updates',
                'Incrementing counters while keeping the rendered value clear.',
                'php-card.svg',
                329,
                ['php-development', 'database-design'],
            ),
            $this->article(
                'docker-file-ownership',
                'Docker File Ownership',
                'Mapping container users to the host user in bind-mounted projects.',
                'devops-card.svg',
                188,
                ['devops-notes'],
            ),
            $this->article(
                'smarty-escaping',
                'Smarty Escaping',
                'Why templates should escape dynamic values by default.',
                'frontend-card.svg',
                267,
                ['frontend-craft', 'php-development'],
            ),
            $this->article(
                'application-bootstrap',
                'Application Bootstrap',
                'Where a small PHP app wires routing, services, and rendering.',
                'architecture-card.svg',
                172,
                ['architecture', 'php-development'],
            ),
            $this->article(
                'mysql-cascade-links',
                'MySQL Cascade Links',
                'Cleaning pivot rows automatically without deleting main records.',
                'database-card.svg',
                223,
                ['database-design'],
            ),
            $this->article(
                'phpstan-level-five',
                'PHPStan Level Five',
                'Static analysis as a useful guardrail for native PHP projects.',
                'php-card.svg',
                199,
                ['php-development', 'architecture'],
            ),
            $this->article(
                'scss-build-flow',
                'SCSS Build Flow',
                'Why source styles live outside public and compiled CSS lives inside it.',
                'frontend-card.svg',
                116,
                ['frontend-craft', 'devops-notes'],
            ),
            $this->article(
                'nginx-static-assets',
                'nginx Static Assets',
                'Serving CSS and images directly while PHP handles dynamic pages.',
                'devops-card.svg',
                154,
                ['devops-notes', 'frontend-craft'],
            ),
            $this->article(
                'pagination-dto',
                'Pagination DTO',
                'Moving offset and page calculations out of controllers.',
                'architecture-card.svg',
                261,
                ['architecture', 'php-development'],
            ),
            $this->article(
                'category-page-sorting',
                'Category Page Sorting',
                'Switching between recent and popular posts with safe query values.',
                'database-card.svg',
                340,
                ['database-design', 'frontend-craft'],
            ),
            $this->article(
                'article-related-posts',
                'Article Related Posts',
                'Finding similar articles by shared categories.',
                'php-card.svg',
                287,
                ['php-development', 'database-design'],
            ),
            $this->article(
                'minimal-router-design',
                'Minimal Router Design',
                'Matching simple URL patterns without bringing in a framework.',
                'architecture-card.svg',
                137,
                ['architecture'],
            ),
            $this->article(
                'dockerized-development-loop',
                'Dockerized Development Loop',
                'A repeatable local workflow for PHP, MySQL, nginx, and Sass.',
                'devops-card.svg',
                372,
                ['devops-notes', 'architecture'],
            ),
        ];
    }

    private function article(
        string $slug,
        string $title,
        string $description,
        string $image,
        int $viewsCount,
        array $categories,
    ): array {
        return [
            'slug' => $slug,
            'title' => $title,
            'description' => $description,
            'image' => '/assets/images/' . $image,
            'views_count' => $viewsCount,
            'categories' => $categories,
            'body' => $this->body($title),
        ];
    }

    private function body(string $title): string
    {
        return sprintf(
            '%s explores one focused part of building a small native PHP blog. '
                . 'The goal is to keep the implementation readable while still showing real application boundaries. '
                . 'The example keeps database access behind repositories, page decisions inside services, '
                . 'and templates focused on presentation. This makes the code easier to inspect, '
                . 'test manually, and explain during review. '
                . 'The same idea scales to the rest of the project: choose simple tools, '
                . 'keep responsibilities explicit, and avoid hiding important behavior behind unnecessary '
                . 'abstractions.',
            $title,
        );
    }
}
