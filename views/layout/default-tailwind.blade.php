@php
global $GROCY_REQUIRED_FRONTEND_PACKAGES;
use Grocy\Helpers\ViteHelper;
@endphp

<!DOCTYPE html>
<html lang="{{ GROCY_LOCALE }}"
	dir="{{ $dir }}"
	class="{{ boolval($userSettings['night_mode_enabled_internal']) ? 'dark' : '' }}">

<head>
	<meta charset="utf-8">
	<meta name="viewport"
		content="width=device-width, initial-scale=1">
	<meta name="robots"
		content="noindex,nofollow">

	<link rel="icon"
		type="image/png"
		sizes="32x32"
		href="{{ $U('/img/icon-32.png?v=', true) }}{{ $version }}">

	@if (GROCY_AUTHENTICATED)
	<link rel="manifest"
		crossorigin="use-credentials"
		href="{{ $U('/manifest') . '?data=' . base64_encode($__env->yieldContent('title') . '#' . $U($_SERVER['REQUEST_URI'])) }}">
	@endif

	<title>@yield('title') | Grocy</title>

	{{-- Load Inter font --}}
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

	{{-- FontAwesome Icons --}}
	<link href="{{ $U('/packages/@fortawesome/fontawesome-free/css/fontawesome.min.css?v=', true) }}{{ $version }}"
		rel="stylesheet">
	<link href="{{ $U('/packages/@fortawesome/fontawesome-free/css/solid.min.css?v=', true) }}{{ $version }}"
		rel="stylesheet">

	{{-- Third-party CSS (DataTables, etc.) - loaded conditionally --}}
	@if(in_array('datatables', $GROCY_REQUIRED_FRONTEND_PACKAGES))
	<link href="{{ $U('/packages/datatables.net-bs4/css/dataTables.bootstrap4.min.css?v=', true) }}{{ $version }}"
		rel="stylesheet">
	<link href="{{ $U('/packages/datatables.net-colreorder-bs4/css/colReorder.bootstrap4.min.css?v=', true) }}{{ $version }}"
		rel="stylesheet">
	<link href="{{ $U('/packages/datatables.net-rowgroup-bs4/css/rowGroup.bootstrap4.min.css?v=', true) }}{{ $version }}"
		rel="stylesheet">
	<link href="{{ $U('/packages/datatables.net-select-bs4/css/select.bootstrap4.min.css?v=', true) }}{{ $version }}"
		rel="stylesheet">
	@endif
	@if(in_array('tempusdominus', $GROCY_REQUIRED_FRONTEND_PACKAGES))
	<link href="{{ $U('/packages/tempusdominus-bootstrap-4/build/css/tempusdominus-bootstrap-4.min.css?v=', true) }}{{ $version }}"
		rel="stylesheet">
	@endif
	@if(in_array('summernote', $GROCY_REQUIRED_FRONTEND_PACKAGES))
	<link href="{{ $U('/packages/summernote/dist/summernote-bs4.css?v=', true) }}{{ $version }}"
		rel="stylesheet">
	@endif
	@if(in_array('fullcalendar', $GROCY_REQUIRED_FRONTEND_PACKAGES))
	<link href="{{ $U('/packages/fullcalendar/dist/fullcalendar.min.css?v=', true) }}{{ $version }}"
		rel="stylesheet">
	@endif
	@if(in_array('daterangepicker', $GROCY_REQUIRED_FRONTEND_PACKAGES))
	<link href="{{ $U('/packages/daterangepicker/daterangepicker.css?v=', true) }}{{ $version }}"
		rel="stylesheet">
	@endif
	@if(in_array('animatecss', $GROCY_REQUIRED_FRONTEND_PACKAGES))
	<link href="{{ $U('/packages/animate.css/animate.min.css?v=', true) }}{{ $version }}"
		rel="stylesheet">
	@endif
	@if(in_array('bootstrap-combobox', $GROCY_REQUIRED_FRONTEND_PACKAGES))
	<link href="{{ $U('/packages/@danielfarrell/bootstrap-combobox/css/bootstrap-combobox.css?v=', true) }}{{ $version }}"
		rel="stylesheet">
	@endif
	@if(in_array('bootstrap-select', $GROCY_REQUIRED_FRONTEND_PACKAGES))
	<link href="{{ $U('/packages/bootstrap-select/dist/css/bootstrap-select.min.css?v=', true) }}{{ $version }}"
		rel="stylesheet">
	@endif

	{{-- Toastr for compatibility --}}
	<link href="{{ $U('/packages/toastr/build/toastr.min.css?v=', true) }}{{ $version }}"
		rel="stylesheet">

	{{-- Bootstrap CSS - needed for third-party component compatibility --}}
	<link href="{{ $U('/packages/bootstrap/dist/css/bootstrap.min.css?v=', true) }}{{ $version }}"
		rel="stylesheet">

	{{-- Vite assets (Tailwind CSS + Alpine.js) --}}
	{!! ViteHelper::Assets('resources/js/app.js', $U('/')) !!}

	@stack('pageStyles')

	@if(file_exists(GROCY_DATAPATH . '/custom_css.html'))
	@php include GROCY_DATAPATH . '/custom_css.html' @endphp
	@endif

	{{-- Grocy Global JS Configuration --}}
	<script>
		var Grocy = { };
		Grocy.Components = { };
		Grocy.Mode = '{{ GROCY_MODE }}';
		Grocy.BaseUrl = '{{ $U('/') }}';
		Grocy.CurrentUrlRelative = "/" + window.location.href.split('?')[0].replace(Grocy.BaseUrl, "");
		Grocy.View = '{{ $viewName }}';
		Grocy.Currency = '{{ GROCY_CURRENCY }}';
		Grocy.EnergyUnit = '{{ GROCY_ENERGY_UNIT }}';
		Grocy.CalendarFirstDayOfWeek = '{{ GROCY_CALENDAR_FIRST_DAY_OF_WEEK }}';
		Grocy.CalendarShowWeekNumbers = {{ BoolToString(GROCY_CALENDAR_SHOW_WEEK_OF_YEAR) }};
		Grocy.LocalizationStrings = {!! $LocalizationStrings !!};
		Grocy.LocalizationStringsQu = {!! $LocalizationStringsQu !!};
		Grocy.FeatureFlags = {!! json_encode($featureFlags) !!};
		Grocy.Webhooks = {
		@if(GROCY_FEATURE_FLAG_LABEL_PRINTER && !GROCY_LABEL_PRINTER_RUN_SERVER)
			"labelprinter" : {
				"hook": "{{ GROCY_LABEL_PRINTER_WEBHOOK }}",
				"extra_data": {!! json_encode(GROCY_LABEL_PRINTER_PARAMS) !!},
				"json": {{ BoolToString(GROCY_LABEL_PRINTER_HOOK_JSON) }}
			}
		@endif
		};

		@if (GROCY_AUTHENTICATED)
		Grocy.UserSettings = {!! json_encode($userSettings) !!};
		Grocy.UserId = {{ GROCY_USER_ID }};
		Grocy.UserPermissions = {!! json_encode($permissions) !!};
		@else
		Grocy.UserSettings = { };
		Grocy.UserId = -1;
		@endif
	</script>
</head>

<body class="bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 font-sans antialiased @if($embedded) embedded @endif"
	x-data="{
		sidebarOpen: localStorage.getItem('sidebarOpen') !== 'false',
		sidebarMobile: false,
		masterDataOpen: {{ in_array($viewName, ['products', 'locations', 'shoppinglocations', 'quantityunits', 'productgroups', 'chores', 'batteries', 'taskcategories', 'userfields', 'userentities']) ? 'true' : 'false' }},
		init() {
			this.$watch('sidebarOpen', value => localStorage.setItem('sidebarOpen', value));
		}
	}">

	@if(!$embedded && GROCY_AUTHENTICATED)
	{{-- Mobile overlay --}}
	<div x-show="sidebarMobile"
		x-transition:enter="transition-opacity ease-linear duration-300"
		x-transition:enter-start="opacity-0"
		x-transition:enter-end="opacity-100"
		x-transition:leave="transition-opacity ease-linear duration-300"
		x-transition:leave-start="opacity-100"
		x-transition:leave-end="opacity-0"
		class="fixed inset-0 z-40 bg-gray-900/50 lg:hidden"
		@click="sidebarMobile = false"
		x-cloak></div>

	{{-- Sidebar --}}
	@include('layout.partials.sidebar-nav')

	{{-- Main content area --}}
	<div class="lg:pl-64 transition-all duration-200"
		:class="{ 'lg:pl-64': sidebarOpen, 'lg:pl-16': !sidebarOpen }">
		{{-- Header --}}
		@include('layout.partials.header-actions')

		{{-- Page content --}}
		<main class="py-4 px-4 sm:px-6 lg:px-8">
			<div id="page-content" class="content-text">
				@yield('content')
			</div>
		</main>
	</div>
	@else
	{{-- Non-authenticated or embedded layout --}}
	<div class="py-4 px-4 sm:px-6 lg:px-8">
		<div id="page-content" class="content-text">
			@yield('content')
		</div>
	</div>
	@endif

	{{-- Toast container --}}
	<div id="toast-container" class="fixed bottom-4 right-4 z-50 space-y-2"></div>

	{{-- Modal container --}}
	<div id="modal-container"></div>

	{{-- JavaScript Dependencies --}}
	<script src="{{ $U('/packages/jquery/dist/jquery.min.js?v=', true) }}{{ $version }}"></script>
	<script src="{{ $U('/packages/bootstrap/dist/js/bootstrap.bundle.min.js?v=', true) }}{{ $version }}"></script>
	<script src="{{ $U('/packages/bootbox/dist/bootbox.min.js?v=', true) }}{{ $version }}"></script>
	<script src="{{ $U('/packages/jquery-serializejson/jquery.serializejson.min.js?v=', true) }}{{ $version }}"></script>
	<script src="{{ $U('/packages/moment/min/moment.min.js?v=', true) }}{{ $version }}"></script>
	@if(!empty($__t('moment_locale') && $__t('moment_locale') != 'x'))<script src="{{ $U('/packages', true) }}/moment/locale/{{ $__t('moment_locale') }}.js?v={{ $version }}"></script>@endif
	<script src="{{ $U('/packages/toastr/build/toastr.min.js?v=', true) }}{{ $version }}"></script>
	<script src="{{ $U('/packages/sprintf-js/dist/sprintf.min.js?v=', true) }}{{ $version }}"></script>
	<script src="{{ $U('/packages/gettext-translator/dist/translator.js?v=', true) }}{{ $version }}"></script>
	<script src="{{ $U('/packages/nosleep.js/dist/NoSleep.min.js?v=', true) }}{{ $version }}"></script>

	@if(in_array('bootstrap-combobox', $GROCY_REQUIRED_FRONTEND_PACKAGES))
	<script src="{{ $U('/packages/@danielfarrell/bootstrap-combobox/js/bootstrap-combobox.js?v=', true) }}{{ $version }}"></script>
	@endif
	@if(in_array('datatables', $GROCY_REQUIRED_FRONTEND_PACKAGES))
	<script src="{{ $U('/packages/datatables.net/js/jquery.dataTables.min.js?v=', true) }}{{ $version }}"></script>
	<script src="{{ $U('/packages/datatables.net-bs4/js/dataTables.bootstrap4.min.js?v=', true) }}{{ $version }}"></script>
	<script src="{{ $U('/packages/datatables.net-colreorder/js/dataTables.colReorder.min.js?v=', true) }}{{ $version }}"></script>
	<script src="{{ $U('/packages/datatables.net-colreorder-bs4/js/colReorder.bootstrap4.min.js?v=', true) }}{{ $version }}"></script>
	<script src="{{ $U('/packages/datatables.net-plugins/filtering/type-based/accent-neutralise.js?v=', true) }}{{ $version }}"></script>
	<script src="{{ $U('/packages/datatables.net-plugins/sorting/chinese-string.js?v=', true) }}{{ $version }}"></script>
	<script src="{{ $U('/packages/datatables.net-rowgroup/js/dataTables.rowGroup.min.js?v=', true) }}{{ $version }}"></script>
	<script src="{{ $U('/packages/datatables.net-rowgroup-bs4/js/rowGroup.bootstrap4.min.js?v=', true) }}{{ $version }}"></script>
	<script src="{{ $U('/packages/datatables.net-select/js/dataTables.select.min.js?v=', true) }}{{ $version }}"></script>
	<script src="{{ $U('/packages/datatables.net-select-bs4/js/select.bootstrap4.min.js?v=', true) }}{{ $version }}"></script>
	@endif
	@if(in_array('tempusdominus', $GROCY_REQUIRED_FRONTEND_PACKAGES))
	<script src="{{ $U('/packages/tempusdominus-bootstrap-4/build/js/tempusdominus-bootstrap-4.min.js?v=', true) }}{{ $version }}"></script>
	@endif
	@if(in_array('summernote', $GROCY_REQUIRED_FRONTEND_PACKAGES))
	<script src="{{ $U('/packages/summernote/dist/summernote-bs4.min.js?v=', true) }}{{ $version }}"></script>
	@if(!empty($__t('summernote_locale') && $__t('summernote_locale') != 'x'))<script src="{{ $U('/packages', true) }}/summernote/dist/lang/summernote-{{ $__t('summernote_locale') }}.js?v={{ $version }}"></script>@endif
	@endif
	@if(in_array('bootstrap-select', $GROCY_REQUIRED_FRONTEND_PACKAGES))
	<script src="{{ $U('/packages/bootstrap-select/dist/js/bootstrap-select.min.js?v=', true) }}{{ $version }}"></script>
	@if(!empty($__t('bootstrap-select_locale') && $__t('bootstrap-select_locale') != 'x'))<script src="{{ $U('/packages', true) }}/bootstrap-select/dist/js/i18n/defaults-{{ $__t('bootstrap-select_locale') }}.js?v={{ $version }}"></script>@endif
	@endif
	@if(in_array('fullcalendar', $GROCY_REQUIRED_FRONTEND_PACKAGES))
	<script src="{{ $U('/packages/fullcalendar/dist/fullcalendar.min.js?v=', true) }}{{ $version }}"></script>
	@if(!empty($__t('fullcalendar_locale') && $__t('fullcalendar_locale') != 'x'))<script src="{{ $U('/packages', true) }}/fullcalendar/dist/locale/{{ $__t('fullcalendar_locale') }}.js?v={{ $version }}"></script>@endif
	@endif
	@if(in_array('daterangepicker', $GROCY_REQUIRED_FRONTEND_PACKAGES))
	<script src="{{ $U('/packages/daterangepicker/daterangepicker.js?v=', true) }}{{ $version }}"></script>
	@endif
	@if(in_array('zxing', $GROCY_REQUIRED_FRONTEND_PACKAGES))
	<script src="{{ $U('/packages/@zxing/library/umd/index.min.js?v=', true) }}{{ $version }}"></script>
	@endif
	@if(in_array('bwipjs', $GROCY_REQUIRED_FRONTEND_PACKAGES))
	<script src="{{ $U('/packages/bwip-js/dist/bwip-js-min.js?v=', true) }}{{ $version }}"></script>
	@endif
	@if(in_array('chartjs', $GROCY_REQUIRED_FRONTEND_PACKAGES))
	<script src="{{ $U('/packages/chart.js/dist/Chart.min.js?v=', true) }}{{ $version }}"></script>
	<script src="{{ $U('/packages/chartjs-plugin-colorschemes/dist/chartjs-plugin-colorschemes.min.js?v=', true) }}{{ $version}}"></script>
	<script src="{{ $U('/packages/chartjs-plugin-doughnutlabel/dist/chartjs-plugin-doughnutlabel.js?v=', true) }}{{ $version }}"></script>
	<script src="{{ $U('/packages/chartjs-plugin-piechart-outlabels/dist/chartjs-plugin-piechart-outlabels.min.js?v=', true) }}{{ $version}}"></script>
	<script src="{{ $U('/packages/chartjs-plugin-trendline/dist/chartjs-plugin-trendline.min.js?v=', true) }}{{ $version}}"></script>
	@endif

	<script src="{{ $U('/js/extensions.js?v=', true) }}{{ $version }}"></script>
	<script src="{{ $U('/js/grocy_menu_layout.js?v=', true) }}{{ $version }}"></script>
	<script src="{{ $U('/js/grocy.js?v=', true) }}{{ $version }}"></script>
	<script src="{{ $U('/js/grocy_dbchangedhandling.js?v=', true) }}{{ $version }}"></script>
	<script src="{{ $U('/js/grocy_wakelockhandling.js?v=', true) }}{{ $version }}"></script>
	<script src="{{ $U('/js/grocy_nightmode.js?v=', true) }}{{ $version }}"></script>
	<script src="{{ $U('/js/grocy_clock.js?v=', true) }}{{ $version }}"></script>

	@if(in_array('datatables', $GROCY_REQUIRED_FRONTEND_PACKAGES))
	<script src="{{ $U('/js/grocy_datatables.js?v=', true) }}{{ $version }}"></script>
	@endif
	@if(in_array('summernote', $GROCY_REQUIRED_FRONTEND_PACKAGES))
	<script src="{{ $U('/js/grocy_summernote.js?v=', true) }}{{ $version }}"></script>
	@endif

	@stack('pageScripts')
	@stack('componentScripts')
	<script src="{{ $U('/viewjs/' . $viewName . '.js?v=', true) }}{{ $version }}"></script>

	@if(file_exists(GROCY_DATAPATH . '/custom_js.html'))
	@php include GROCY_DATAPATH . '/custom_js.html' @endphp
	@endif
</body>

</html>
