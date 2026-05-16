{extends file="layouts/main.tpl"}

{block name="content"}
    <article>
        <h1>{$article->title}</h1>
        <p>{$article->description}</p>
        <p>{$article->body}</p>
    </article>
{/block}
