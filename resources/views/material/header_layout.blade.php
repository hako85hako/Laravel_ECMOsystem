<head>
  <title>Laravel Sample</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <link rel="stylesheet" href="{{ asset('css/public.css') }}">
  <script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js"></script>
</head>
<body>
<div class="container-fluid">
	<div class="row" id="header">
		<div class="col text-center">
			<h2>To think ECMO</h2>
		</div>
    </div>
</div>
@yield('content')
</body>

