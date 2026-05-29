<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>{{ $result['title'] }} | {{ App_Name() }}</title>

        <link href="https://fonts.googleapis.com/css2?family=Rubik:wght@400;500;600;700&display=swap" rel="stylesheet">

        <style type="text/css">
            :root {
                --title-color: {{ $settings['page_title_color'] }};
                --background-color: {{ $settings['page_background_color'] }};
            }
            body {
                font-family: 'Rubik', sans-serif;
                background-color: var(--background-color);
                margin: 0px;
            }
            .page-title{
                text-align: center;
                margin: 15px;
                font-size: 36px;
                color: var(--title-color);
                border-bottom: 2px solid var(--title-color);
            }
            .description{
                margin: 15px;
            }
        </style>
    </head>

    <body>
        <p class="page-title">{{ $result['title'] }}</p>
        <div class="description">
            <?php echo htmlspecialchars_decode($result['description']); ?>
        </div>
    </body>
</html>