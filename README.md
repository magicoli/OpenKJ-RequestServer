# OpenKJ Standalone Request Server

This fork is a work in progress. It is an attempt to mordernize the presentation and the coding methods of the original [OpenKJ/StandaloneRequestServer](https://github.com/OpenKJ/StandaloneRequestServer). While it is functional, it does not fit the goals yet.

## Original README:

Standalone basic single-venue request server implementation for use with OpenKJ.

Note: This is intended for people who already know how to configure and manage their own webservers and have a general familiarity with php.  The easier and more feature rich option is to use the hosted service available at https://okjsongbook.com

Requires php

Can be run under either php's built in web server or under any web server with php support like apache or nginx.

Ignores any API key specified in the OpenKJ.

If you were serving this from a web server as http://10.0.0.1/requestserver, you would configure the server URL in OpenKJ to point to http://10.0.0.1/requestserver/api.php 

config/settings.php should be edited with an appropriate database path that the webserver has write access to.  If the database file doesn't exist, it will be created automatically.
