<?php
declare(strict_types=1);

namespace StepTheFkUp\Pagination\Tests\Bridge\Laravel;

use Illuminate\Pagination\LengthAwarePaginator as IlluminateLengthAwarePaginator;
use StepTheFkUp\Pagination\Bridge\Laravel\LengthAwarePaginator;
use StepTheFkUp\Pagination\Interfaces\PaginationConstants;
use StepTheFkUp\Pagination\Tests\AbstractTestCase;

class LengthAwarePaginatorTest extends AbstractTestCase
{
    /**
     * Paginator should return expected output data based on input data.
     *
     * @return void
     */
    public function testGetters(): void
    {
        $items = [];
        $page = PaginationConstants::DEFAULT_PAGE;
        $perPage = PaginationConstants::DEFAULT_PER_PAGE;
        $total = 100;

        $paginator = new LengthAwarePaginator(new IlluminateLengthAwarePaginator($items, $total, $perPage, $page));

        self::assertEquals($page, $paginator->getCurrentPage());
        self::assertEquals($items, $paginator->getItems());
        self::assertEquals($perPage, $paginator->getItemsPerPage());
        self::assertEquals($total, $paginator->getTotalItems());
        self::assertEquals(10, $paginator->getTotalPages());
        self::assertTrue($paginator->hasNextPage());
        self::assertFalse($paginator->hasPreviousPage());
    }
}