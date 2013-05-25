smallMVC
========

smallMVC is a small PHP MVC framework

INSTRUCTIONS:<br />
1. Create new web directory<br />
2. Move framework to the newly created web directory<br />
3. cd into the directory above<br />
4. Run framework/generate.php and follow the instructions<br />

AVAILABLE OPERATIONS:<br />
1. Generate a new website<br />
2. 2. Generate a new controller<br />
3. 3. Generate a new model<br />
4. 4. List the directory<br />
5. 5. Delete the website<br />
6. 0. Exit<br />

GENERATED DIRECTORY STRUCTURE:<br />
css/<br />
framework/<br />
images/<br />
js/<br />
lib/<br />
protected/<br />
         /components     - contains Constants class<br />
         /config         - database connection configuration<br />
         /controllers    - all controllers are here<br />
         /data           - database SQL source code<br />
         /models         - all models are here<br />
         /views/         - all views are here<br />
               /layouts  - main layouts<br />
               /web      - specific controllers views, called from controllers actions<br />
