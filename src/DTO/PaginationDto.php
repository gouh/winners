<?php

namespace App\DTO;

class PaginationDto
{
    /** @var int */
    public int $totalItems;
    /** @var int */
    public int $itemsPerPage;
    /** @var int */
    public int $totalInPage;
    /** @var int */
    public int $currentPage;
    /** @var int */
    public int $totalPages;
    /** @var int[] */
    public array $prevPages;
    /** @var int[] */
    public array $nextPages;

    /** @var int|null */
    public ?int $prevPage;
    /** @var int|null */
    public ?int $nextPage;

    public function __construct(
        $totalItems,
        $itemsPerPage,
        $totalInPage,
        $currentPage,
        $totalPages,
        $prevPages,
        $nextPages,
        $prevPage,
        $nextPage,
    ) {
        $this->totalItems = $totalItems;
        $this->itemsPerPage = $itemsPerPage;
        $this->totalInPage = $totalInPage;
        $this->currentPage = $currentPage;
        $this->totalPages = $totalPages;
        $this->prevPages = $prevPages;
        $this->nextPages = $nextPages;
        $this->prevPage = $prevPage;
        $this->nextPage = $nextPage;
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }

    /**
     * @param int $page
     * @param int $totalInPage
     * @param int $totalItems
     * @param int $itemsPerPage
     * @param int $limitAround
     *
     * @return PaginationDto
     */
    public static function calcPagination(
        int $page,
        int $totalInPage,
        int $totalItems,
        int $itemsPerPage = 30,
        int $limitAround = 5
    ): PaginationDto {
        $itemsPerPage = max(1, $itemsPerPage);
        $totalItems = max(0, $totalItems);
        $totalPages = max(1, ceil($totalItems / $itemsPerPage));
        $currentPage = min(max(1, $page), $totalPages);

        $previousStart = max(1, $currentPage - $limitAround);
        $previousEnd = max(1, min($currentPage - 1, $previousStart + $limitAround - 1));
        $prevPages = $previousStart < $currentPage ? range($previousStart, $previousEnd) : [];

        $nextStart = min($currentPage + 1, $totalPages);
        $nextEnd = min($totalPages, $nextStart + $limitAround - 1);
        $nextPages = $currentPage < $totalPages ? range($nextStart, $nextEnd) : [];

        return new PaginationDto(
            $totalItems,
            $itemsPerPage,
            $totalInPage,
            $page,
            $totalPages,
            $prevPages,
            $nextPages,
            count($prevPages) ? end($prevPages) : null,
            count($nextPages) ? $nextPages[0] : null,
        );
    }
}