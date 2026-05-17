{if $pagination->getTotalPages() > 1}
    <nav class="pagination" aria-label="Pagination">
        {if $pagination->hasPreviousPage()}
            <a class="pagination__link" href="{$pagination->buildPageUrl($baseUrl, $pagination->getPreviousPage(), $sortQuery)}">Previous</a>
        {/if}

        <span class="pagination__status">Page {$pagination->page} of {$pagination->getTotalPages()}</span>

        {if $pagination->hasNextPage()}
            <a class="pagination__link" href="{$pagination->buildPageUrl($baseUrl, $pagination->getNextPage(), $sortQuery)}">Next</a>
        {/if}
    </nav>
{/if}
