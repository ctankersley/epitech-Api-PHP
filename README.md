__Epitech-Api-PHP__
-------------------
Simple Api for requesting the intranet.

__Utilisation de l'api__

Toutes les fonctionnalités se trouvent dans le fichier API.php

------------------------------------------------------------
Le Webservice permet d'effectuer des requêtes de type GET ou POST, l'authentification (login/password) ainsi qu'une action est requise.

__Format des réponses__
-----------------------
Les réponses du Webservice sont faites en JSON  
Example d'url :
<pre><code>
http://MYURL.com/API.php?login=MYLogin&password=MYPASSWORD&action=login
<code></pre>
Réponse :
<pre><code>
{"login":"1"}
<code></pre>
