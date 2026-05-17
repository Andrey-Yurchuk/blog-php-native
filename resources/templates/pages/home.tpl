{extends file="layouts/main.tpl"}

{block name="content"}
    <div class="site-shell blog-home">
        {if $categories|count > 0}
            {foreach $categories as $categoryBlock}
                <section class="category-section">
                    <div class="section-heading">
                        <h1 class="section-heading__title">{$categoryBlock->category->title}</h1>
                        <a class="text-link" href="/category/{$categoryBlock->category->slug}">View All</a>
                    </div>

                    <div class="article-grid article-grid--three">
                        {foreach $categoryBlock->articles as $article}
                            {include file="partials/article-card.tpl" article=$article}
                        {/foreach}
                    </div>
                </section>
            {/foreach}
        {else}
            <section class="empty-state">
                <h1>No articles yet</h1>
                <p>Published categories will appear here.</p>
            </section>
        {/if}
    </div>
{/block}
