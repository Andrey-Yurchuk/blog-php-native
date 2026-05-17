<article class="article-card">
    <a class="article-card__image-link" href="/article/{$article->slug}" aria-label="{$article->title}">
        <img class="article-card__image" src="{$article->image}" alt="{$article->title}" loading="lazy">
    </a>

    <div class="article-card__body">
        <h2 class="article-card__title">
            <a href="/article/{$article->slug}">{$article->title}</a>
        </h2>
        <time class="article-card__date" datetime="{$article->publishedAt}">{$article->publishedAt}</time>
        <p class="article-card__description">{$article->description}</p>
        <a class="text-link" href="/article/{$article->slug}">Continue Reading</a>
    </div>
</article>
