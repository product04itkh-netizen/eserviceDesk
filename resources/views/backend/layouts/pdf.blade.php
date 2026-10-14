<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', app_name())</title>
  <meta name="description" content="@yield('meta_description', 'Laravel 5 Boilerplate')">
  <meta name="author" content="@yield('meta_author', 'Anthony Rappa')">
  @yield('meta')

  @stack('before-styles')

  <!-- Check if the language is set to RTL, so apply the RTL layouts -->
  <!-- Otherwise apply the normal LTR layouts -->
  <link href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css" rel="stylesheet">
  <link href="https://cdn.datatables.net/buttons/1.5.6/css/buttons.dataTables.min.css" rel="stylesheet">
  @stack('after-styles')
</head>
<body>
@yield('content')
@stack('before-scripts')
{!! script(mix('js/manifest.js')) !!}
{!! script(mix('js/vendor.js')) !!}
{!! script(mix('js/backend.js')) !!}

{!! script(mix('js/gijgo.js')) !!}
<script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js" defer></script>
<script src="https://cdn.datatables.net/buttons/1.5.6/js/dataTables.buttons.min.js" defer></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js" defer></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js" defer></script>
<script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.html5.min.js" defer></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js" defer></script>
<script src="https://cdn.datatables.net/plug-ins/1.10.19/sorting/currency.js" defer></script>
<script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.colVis.min.js" defer></script>
<script src="{{ asset('js/jQuery.print.min.js') }}"></script>
@stack('after-scripts')
</body>
</html>
