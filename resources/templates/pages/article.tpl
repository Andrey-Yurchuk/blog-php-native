{extends file="layouts/main.tpl"}

{block name="content"}
    <div class="site-shell page-section">
        <article class="article-detail">
            <img class="article-detail__image" src="{$article->image}" alt="{$article->title}">

            <div class="article-detail__content">
                <div class="article-meta">
                    <time datetime="{$article->publishedAt}">{$article->publishedAt}</time>
                    <span>{$article->viewsCount} views</span>
                </div>

                <h1>{$article->title}</h1>
                <p class="article-detail__lead">{$article->description}</p>

                {if $categories|count > 0}
                    <ul class="category-list">
                        {foreach $categories as $category}
                            <li><a href="/category/{$category->slug}">{$category->title}</a></li>
                        {/foreach}
                    </ul>
                {/if}

                <div class="article-body">
                    {$article->body}
                </div>
            </div>
        </article>

        {if $relatedArticles|count > 0}
            <section class="category-section category-section--related">
                <div class="section-heading">
                    <h2 class="section-heading__title">Related Articles</h2>
                </div>

                <div class="article-grid article-grid--three">
                    {foreach $relatedArticles as $article}
                        {include file="partials/article-card.tpl" article=$article}
                    {/foreach}
                </div>
            </section>
        {/if}
    </div>
{/block}
