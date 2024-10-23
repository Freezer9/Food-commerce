<?php

$dbhost = 'localhost';
$dbuser = 'postgres';
$dbpass = 'pens2022';
$dbname = 'onlinefood';

$conn = pg_connect("host=$dbhost port=5432 dbname=$dbname user=$dbuser password=$dbpass") or die('Error connecting to postgresql');
