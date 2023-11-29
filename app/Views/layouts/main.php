<!DOCTYPE html>
<html dir="ltr" lang="">
<head>

    <link rel="icon" type="image/vnd.microsoft.icon" href="/favicon.ico" />

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Parser</title>

    <script type="text/javascript" src="/js/jquery-3.2.1.min.js"></script>
    <script type="text/javascript" src="/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="/js/custom.js"></script>
    <link rel="stylesheet" href="/css/bootstrap.min.css">
    <link rel="stylesheet" href="/css/main.css">

</head>
<body>
<div class="header">

</div>
<div class="body">
    <?= $this->renderSection('upload') ?>
    <?= $this->renderSection('results') ?>
    <?= $this->renderSection('content') ?>
    <?= $this->renderSection('login') ?>
    <?= $this->renderSection('error') ?>
</div>
<div class="footer"></div>

</body>
</html>