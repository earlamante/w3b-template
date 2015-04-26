<?php
/**
 * This is the error 404 "Page Not Found" with the W3B Template skin
 *
 * @since Version 1.3
 */
	if(empty($url))
		$url = explode('index.php', $_SERVER['PHP_SELF'])[0].'data/';
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Page Not Found</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>

        * {
            line-height: 1.2;
            margin: 0;
        }

        html {
            color: #888;
            display: table;
            font-family: sans-serif;
            height: 100%;
            text-align: center;
            width: 100%;
        }

        body {
            display: table-cell;
            vertical-align: middle;
            margin: 2em auto;
        }

        h1 {
            color: #555;
            font-size: 2em;
            font-weight: 400;
            margin-bottom: 16px;
        }

        p {
            margin: 0 auto 20px;
            width: 280px;
            line-height: 1.5em;
        }
		
		img {
			width: 100px;
		}

        @media only screen and (max-width: 280px) {

            body, p {
                width: 95%;
            }

            h1 {
                font-size: 1.5em;
                margin: 0 0 0.3em;
            }

        }

    </style>
</head>
<body>
    <h1>Page Not Found</h1>
    <p>Sorry, but the page you were trying to view does not exist.</p>
	<p>Presented by: <a href="http://w3bkit.com"><br /><img src="<?php echo $url; ?>img/w3bkit.svg" alt="logo" /></a></p>
</body>
</html>
<!-- IE needs 512+ bytes: http://blogs.msdn.com/b/ieinternals/archive/2010/08/19/http-error-pages-in-internet-explorer.aspx -->