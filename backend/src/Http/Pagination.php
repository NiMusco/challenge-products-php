<?php

declare(strict_types=1);

namespace App\Http;

final class Pagination
{
    private const DEFAULT_PAGE = 1;
    private const DEFAULT_PER_PAGE = 5;
    private const MAX_PER_PAGE = 100;

    public function __construct(
        public readonly int $page,
        public readonly int $perPage,
    ) {
    }

    public static function buildFromQueryParams(): self
    {
        $page = max(1, (int) ($_GET['page'] ?? self::DEFAULT_PAGE));
        $perPage = max(1, min(self::MAX_PER_PAGE, (int) ($_GET['per_page'] ?? self::DEFAULT_PER_PAGE)));

        return new self($page, $perPage);
    }

    public function totalPages(int $total): int
    {
        return $total > 0
            ? (int) ceil($total / $this->perPage)
            : 0;
    }

    public function applyHeaders(int $total): void
    {
        header('X-Page: ' . $this->page);
        header('X-Per-Page: ' . $this->perPage);
        header('X-Total-Count: ' . $total);
        header('X-Total-Pages: ' . $this->totalPages($total));
    }
}
