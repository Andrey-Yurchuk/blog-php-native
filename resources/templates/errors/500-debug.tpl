{extends file="layouts/main.tpl"}

{block name="content"}
    <div class="site-shell page-section">
        <section class="empty-state empty-state--error error-page">
            <div class="error-page__content">
                <h1>Server error</h1>
                <p class="error-page__message">{$errorMessage}</p>
                <p class="error-page__type">{$errorType}</p>
                <pre class="error-page__trace">{$errorTrace}</pre>
                <p><a class="text-link" href="/">Back to home</a></p>
            </div>
        </section>
    </div>
{/block}
