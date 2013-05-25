smallMVC
========

smallMVC is a small PHP MVC framework

INSTRUCTIONS:
1. Create new web directory
2. Move framework to the newly created web directory
3. cd into the directory above
4. Run framework/generate.php and follow the instructions

AVAILABLE OPERATIONS:
1. Generate a new website
2. Generate a new controller
3. Generate a new model
4. List the directory
5. Delete the website
0. Exit

GENERATED DIRECTORY STRUCTURE:
css/
framework/
images/
js/
lib/
protected/
         /components     - contains Constants class
         /config         - database connection configuration
         /controllers    - all controllers are here
         /data           - database SQL source code
         /models         - all models are here
         /views/         - all views are here
               /layouts  - main layouts
               /web      - specific controllers views, called from controllers actions
