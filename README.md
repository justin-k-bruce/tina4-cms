# Tina4 CMS Module

Happy you have decided to try this, how does it work?

```
composer require andrevanzuydam/tina4cms
php -S localhost:8080 index.php
```

Make the usual tina4 index.php file - we do need a database!

```
require_once "vendor/autoload.php";

global $DBA;

$DBA = new \Tina4\DataSQLite3("test.db","", "", "d/m/Y");

echo new \Tina4\Tina4Php();
```

Open up the CMS to setup the admin user

http://localhost:8080/cms/login -> will get you started

### The Landing Page - home

You need to create a landing page called "home" as your starting page for things to working properly.

### Customization

Make a  *base.twig* file in your */src/templates* folder, it needs the following blocks
```
<!DOCTYPE html>
<html lang="en">
<head>
    <title>{{ title }}</title>
    <meta prefix="og: https://ogp.me/ns#" property="og:title" content="{{ title }}"/>
    <meta prefix="og: https://ogp.me/ns#" property="og:type" content="website"/>
    <meta prefix="og: https://ogp.me/ns#" property="og:url" content="{{ url }}"/>
    <meta prefix="og: https://ogp.me/ns#" property="og:image" content="{{ image }}"/>
    <meta prefix="og: https://ogp.me/ns#" property="og:description" content="{{ description }}"/>
{% block headers %}
{% endblock %}
</head>
{% block body %}
<body>
{% block navigation %}
    {% include "navigation.twig" %}
{% endblock %}

{% block content %}
{% endblock %}

{% block footer %}
{% endblock %}
</body>
{% endblock %}
</html>
```
or an example which extends the existing base in the tina4-cms
```
{% extends "@tina4cms/base.twig" %}

{% block headers %}
    <link rel="stylesheet" type="text/css" href="/src/templates/css/default.css">
{% endblock %}

{% block body %}
<body>
    <div class="content">
{% block navigation %}
    {%  include "navigation.twig" %}
{% endblock %}

{% block content %}
{% endblock %}
    </div>
</body>
{% endblock %}
```


#### Example of a navigation.twig which you can over write
Create a *navigation.twig* file in your *src/templates* folder
```
{% set menus = Content.getMenu("") %}
<nav>
    <ul>
        {% for menu in menus %}
            <li><a href="{{ menu.url }}">{{ menu.name }}</a>
                    {% if menu.children %}<ul>{% for menu in menu.children %}<a href="{{ menu.url }}">{{ menu.name }}</a> </ul>{% endfor %}{% endif %}
                    </li>
        {% endfor %}
    </ul>
</nav>
```

### Including your snippets in the CMS
 
There are two ways you can do this:

When you want to include content as it is, and not have the snippet parsed with Twig you can simply use the following:
Use the raw filter when you want to have scripts or other things included correctly
```
{{snippetName | raw}} or {{snippetName}}
```

The following is how you would include a snippet where you want variables in the page for example parsed in the snippet
```
{{ include(getSnippet("snippetName")) }}
```

#### Example:

Page content of "home"
```
  {% set world = "World!" %}
  
  {{ include (getSnippet("mySnippet")) }}
```

Snippet content of "mySnippet"
```
  Hello {{world}}!
  
```

### CMS Events

Generic Code Example:
``` 
(new \Tina4\Event())->onTrigger("beforePageUpdate", function (Page $page, \Tina4\Request $request) {
    //Your own code here
    //$page->random = "random";

    //Manipulate parsed variables from form
    //$request->params["testVariable"];
});
```


| Events           | Variables                             |
|------------------|---------------------------------------|
| loadPageContent  | object $contentObject, $pageName      |
| beforePageCreate | object Page,  \Tina4\Request $request |
| afterPageCreate  | object Page,  \Tina4\Request $request |
| beforePageUpdate | object Page,  \Tina4\Request $request |
| afterPageUpdate  | object Page,  \Tina4\Request $request |
| beforePageDelete | object Page,  \Tina4\Request $request |
| afterPageDelete  | object Page,  \Tina4\Request $request |
| beforePageFetch  | object Page,  \Tina4\Request $request |
