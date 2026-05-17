<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{$pageTitle|default:"Blogy"}</title>
    <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body>
    <header class="site-header">
        <div class="site-shell site-header__inner">
            <a class="site-logo" href="/">Blogy</a>
        </div>
    </header>

    <main class="site-main">
        {block name="content"}{/block}
    </main>

    <footer class="site-footer">
        <div class="site-shell">
            <p>Copyright ©2026. All Rights Reserved.</p>
        </div>
    </footer>
</body>
</html>
