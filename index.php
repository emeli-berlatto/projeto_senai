<?php

define('PUBLIC_URL', $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST']);

header("location: public/view/index.shtml");