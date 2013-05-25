smallMVC
========

smallMVC is a small PHP MVC framework

<h3>INSTRUCTIONS:</h3>
<ol>
<li>Create new web directory</li>
<li>Move framework to the newly created web directory</li>
<li>cd into the directory above</li>
<li>Run framework/generate.php and follow the instructions</li>
</ol>

<h3>AVAILABLE OPERATIONS:</h3>
<ol start="0">
<li>Exit</li>
<li>Generate a new website</li>
<li>Generate a new controller</li>
<li>Generate a new model</li>
<li>List the directory</li>
<li>Delete the website</li>
</ol>

<h3>GENERATED DIRECTORY STRUCTURE:</h3>
<ul>
<li>css/</li>
<li>framework/</li>
<li>images/</li>
<li>js/</li>
<li>lib/</li>
<li>protected/</li>

         <ul>
         <li>/components     - contains Constants class</li>
         <li>/config         - database connection configuration</li>
         <li>/controllers    - all controllers are here</li>
         <li>/data           - database SQL source code</li>
         <li>/models         - all models are here</li>
         <li>/views/         - all views are here</li>

                  <ul>
                  <li>/layouts  - main layouts</li>
                  <li>/web      - specific controllers views, called from controllers actions</li>
                  </ul>

         </ul>

</ul>
