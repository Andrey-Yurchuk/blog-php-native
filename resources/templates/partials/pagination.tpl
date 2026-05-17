{if $pagination->getTotalPages() > 1}
    <nav class="pagination" aria-label="Pagination">
        {if $pagination->hasPreviousPage()}
            <a class="pagination__link" href="{$baseUrl}?sort={$sort}&page={$pagination->getPreviousPage()}">Previous</a>
        {/if}

        <span class="pagination__status">Page {$pagination->page} of {$pagination->getTotalPages()}</span>

        {if $pagination->hasNextPage()}
            <a class="pagination__link" href="{$baseUrl}?sort={$sort}&page={$pagination->getNextPage()}">Next</a>
        {/if}
    </nav>
{/if}
