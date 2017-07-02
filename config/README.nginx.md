You'll need to open your php.ini configuration file and look for two values:

- upload_max_filesize

Maximum allowed size for uploaded files (default: 2 megabytes).
You need to increase this value if you expect files over 2 megabytes in size.

- post_max_size

Maximum size of POST data that PHP will accept (default: 8 megabytes).
Files are sent via POST data, so you need to increase this value if you are
expecting files over 8 megabytes.
