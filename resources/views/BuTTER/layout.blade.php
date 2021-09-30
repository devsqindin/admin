<!DOCTYPE html>

<html>

<head>

	<title>@yield('title','BuTTER')</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.8.2/css/all.min.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bulma/0.7.4/css/bulma.min.css">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/datatables-bulma@1.0.1/css/dataTables.bulma.min.css">

	@stack('css')

</head>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.19/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/datatables-bulma@1.0.1/js/dataTables.bulma.min.js"></script>

<body>


	<br>
	<div class='container'>
	@yield('content', 'baba is you')
	</div>

	<div class='container'>
		<a class='button' href="/">
			<span class='icon is-small'>
				<i class='fas fa-home'></i>
			</span>
			<span>Home</span>
		</a>
	</div>

	@stack('js')
</body>

</html>
