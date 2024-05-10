<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php echo($browser_title); ?></title>
    <style>
        * {
            box-sizing: border-box;
        }

        html, body {
            margin: 0;
            padding: 0;
            font-family: sans-serif;
            font-size: 1rem;
            background: #333333;
            color: #CCCCCC;
        }

        #header {
            padding: 10px;
            font-size: 1.6rem;
            font-weight: bold;
            color: #EEEEEE;
            background: #000000;
            border-bottom: 1px dashed #999999;
        }

        #content {
            padding: 10px;
        }

        #content form {
            margin: 20px;
            width: 400px;
        }

        #content form div {
            margin: 15px 0;
        }

        #content form label {
            display: block;
            padding: 0 0 5px 0;
            font-weight: bold;
        }

        #content form input {
            padding: 5px;
        }

        #content form input[type="email"] {
            display: block;
            width: 100%;
        }

        #content form button {
            display: block;
            width: 100%;
            font-weight: bold;
            cursor: pointer;
            font-size: 1.1rem;
        }

        #content form button:hover {
            background: #CCCCCC;
        }

        pre {
            border: 1px dashed #999999;
            padding: 5px;
            font-family: 'Courier New', Courier, monospace;
            font-size: 0.9rem;
            background: #222222;
        }

        footer {
            margin: 10px 0 0 0;
            padding: 10px;
            font-size: 0.8rem;
            color: #EEEEEE;
            background: #000000;
            border-top: 1px dashed #999999;
        }

        h1 {
            font-size: 1.4rem;
        }

        h2 {
            font-size: 1.3rem;
        }

        h3 {
            font-size: 1.2rem;
        }
    </style>
</head>
<body>
