<html>

<head>
  <title><?= $this->e($title) ?></title>
  <!--Import Google Icon Font-->
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <!--Import materialize.css-->
  <link type="text/css" rel="stylesheet" href="/assets/css/materialize.min.css" media="screen,projection" />
  <link type="text/css" rel="stylesheet" href="/assets/css/ghpages-materialize.css" media="screen,projection" />
  <link type="text/css" rel="stylesheet" href="/assets/css/style.css?v=3" media="screen,projection" />
  <!--Let browser know website is optimized for mobile-->
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel='shortcut icon' type='image/x-icon' href='/assets/img/favicon.ico' />

</head>

<body>
  <div class="container">
    <!-- Page Content goes here -->
    <?= $this->section('content') ?>
  </div>
  <script type="text/javascript" src="/assets/js/materialize.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</body>

</html>