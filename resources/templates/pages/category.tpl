{extends file="layouts/main.tpl"}

{block name="content"}
    <div class="site-shell page-section">
        <header class="page-header">
            <div>
                <h1>{$category->title}</h1>
                <p>{$category->description}</p>
            </div>

            <nav class="sort-nav" aria-label="Article sorting">
                <a class="sort-nav__link{if $sort == 'published_at'} sort-nav__link--active{/if}" href="/category/{$category->slug}?sort=published_at">Newest</a>
                <a class="sort-nav__link{if $sort == 'views_count'} sort-nav__link--active{/if}" href="/category/{$category->slug}?sort=views_count">Most viewed</a>
            </nav>
        </header>

        {if $articles->items|count > 0}
            <div class="article-grid article-grid--three">
                {foreach $articles->items as $article}
                    {include file="partials/article-card.tpl" article=$article}
                {/foreach}
            </div>

            {include file="partials/pagination.tpl" pagination=$articles->pagination baseUrl="/category/{$category->slug}" sort=$sort}
        {else}
            <section class="empty-state empty-state--compact">
                <h2>No articles in this category</h2>
                <p>New posts will appear here after publishing.</p>
            </section>
        {/if}
    </div>
{/block}
