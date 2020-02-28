<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Weather app</title>

        <link href="/css/main.css" rel="stylesheet">
        <script src="https://rawgithub.com/darkskyapp/skycons/master/skycons.js" defer></script>
        <script src="/js/app.js" defer></script>
    </head>
    <body class="bg-blue-200">
        <div id="app" class="flex justify-center pt-16">
            <weather-app></weather-app>
        </div>
    </body>
</html>
