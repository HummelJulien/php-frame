
<!DOCTYPE html>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
</head>
<body>

        <p>
        {% if (isset($allRoutes) && is_array($allRoutes)) { %}
        <table class="container">
            <caption><h1>Debug routes</h1></caption>
            <tr class="mr-2 border-bottom bg-light">
                <th scope="col">Route name</th>
                <th scope="col">Path</th>
                <th scope="col">Controller</th>
                <th scope="col">Action</th>
                <th scope="col">Methode</th>
            </tr>
            {% foreach ($allRoutes as $route) { %}
            <tr class="border-bottom">
                <th class="mr-5"  scope="row">{{ $route->getRouteName() }}</th>
                <td class="mr-5">{{ $route->getRouteUri() }}</td>
                <td class="mr-5">{{ $route->getControllerName() }}</td>
                <td class="mr-5">{{ $route->getAction() }}</td>
                <td class="mr-5">{% if (!empty($route->getMethodes())) { foreach ($route->getMethodes() as $methodes) { echo $methodes .' '; }
                    }
                    else {
                        echo 'ALL';
                    } %}</td>

            </tr>
            {% } %}
        </table>
        {% } else { %}
            Le router est crasher
        {% } %}
        </p>
</body>