@extends('layout.default')

@section('title', $__t('Consume'))

@push('pageScripts')
<script src="{{ $U('/js/grocy_uisound.js?v=', true) }}{{ $version }}"></script>
@endpush

@section('content')
<script>
	Grocy.QuantityUnits = {!! json_encode($quantityUnits) !!};
	Grocy.QuantityUnitConversionsResolved = {!! json_encode($quantityUnitConversionsResolved) !!};
	Grocy.DefaultMinAmount = '{{$DEFAULT_MIN_AMOUNT}}';
</script>

<div class="flex flex-wrap">
	<div class="w-full md:w-1/2 xl:w-1/3 pb-3">
		<div class="flex flex-wrap items-center gap-2">
			<h2 class="text-xl font-bold text-gray-900 dark:text-gray-100">@yield('title')</h2>
			<button class="btn btn-secondary md:hidden mt-2 float-right order-1 md:order-3 hide-when-embedded"
				type="button"
				data-toggle="collapse"
				data-target="#related-links">
				<i class="fa-solid fa-ellipsis-v"></i>
			</button>
			<div class="related-links collapse md:flex order-2 w-full md:w-auto"
				id="related-links">
				@if(!$embedded)
				<button id="scan-mode-button"
					class="btn @if(boolval($userSettings['scan_mode_consume_enabled'])) btn-success @else btn-danger @endif m-1 md:mt-0 md:mb-0 float-right"
					data-toggle="tooltip"
					title="{{ $__t('When enabled, after changing/scanning a product and if all fields could be automatically populated (by product and/or barcode defaults), the transaction is automatically submitted') }}">{{ $__t('Scan mode') }} <span id="scan-mode-status">@if(boolval($userSettings['scan_mode_consume_enabled'])) {{ $__t('on') }} @else {{ $__t('off') }} @endif</span></button>
				<input id="scan-mode"
					type="checkbox"
					class="hidden user-setting-control"
					data-setting-key="scan_mode_consume_enabled"
					@if(boolval($userSettings['scan_mode_consume_enabled']))
					checked
					@endif>
				@else
				<script>
					Grocy.UserSettings.scan_mode_consume_enabled = false;
				</script>
				@endif
			</div>
		</div>

		<hr class="my-2 border-gray-200 dark:border-gray-700">

		<form id="consume-form"
			novalidate>

			@include('components.productpicker', array(
			'products' => $products,
			'barcodes' => $barcodes,
			'nextInputSelector' => '#amount',
			'disallowAddProductWorkflows' => true
			))

			<div id="consume-exact-amount-group"
				class="form-group hidden">
				<label class="inline-flex items-center gap-2 cursor-pointer">
					<input class="checkbox"
						type="checkbox"
						id="consume-exact-amount"
						name="consume-exact-amount"
						value="1">
					<span class="text-sm text-gray-700 dark:text-gray-300">{{ $__t('Consume exact amount') }}</span>
				</label>
			</div>

			@include('components.productamountpicker', array(
			'value' => 1,
			'additionalHtmlContextHelp' => '<div id="tare-weight-handling-info"
				class="text-blue-600 dark:text-blue-400 italic hidden">' . $__t('Tare weight handling enabled - please weigh the whole container, the amount to be posted will be automatically calculcated') . '</div>'
			))

			<div class="form-group @if(!GROCY_FEATURE_FLAG_STOCK_LOCATION_TRACKING) hidden @endif">
				<label class="form-label" for="location_id">{{ $__t('Location') }}</label>
				<select required
					class="select location-combobox"
					id="location_id"
					name="location_id">
					<option></option>
					@foreach($locations as $location)
					<option value="{{ $location->id }}">{{ $location->name }}</option>
					@endforeach
				</select>
				<div class="form-error hidden">{{ $__t('A location is required') }}</div>
			</div>

			<div class="form-group">
				<label class="inline-flex items-center gap-2 cursor-pointer">
					<input class="checkbox"
						type="checkbox"
						id="spoiled"
						name="spoiled"
						value="1">
					<span class="text-sm text-gray-700 dark:text-gray-300">{{ $__t('Spoiled') }}</span>
				</label>
			</div>

			<div class="form-group">
				<label class="inline-flex items-center gap-2 cursor-pointer">
					<input class="checkbox"
						type="checkbox"
						id="use_specific_stock_entry"
						name="use_specific_stock_entry"
						value="1">
					<span class="text-sm text-gray-700 dark:text-gray-300">{{ $__t('Use a specific stock item') }}
						&nbsp;<i class="fa-solid fa-question-circle text-gray-500 dark:text-gray-400"
							data-toggle="tooltip"
							data-trigger="hover click"
							title="{{ $__t('The first item in this list would be picked by the default rule consume rule (Opened first, then first due first, then first in first out)') }}"></i>
					</span>
				</label>
				<select disabled
					class="select mt-2"
					id="specific_stock_entry"
					name="specific_stock_entry">
					<option></option>
				</select>
			</div>

			@if (GROCY_FEATURE_FLAG_RECIPES)
			@include('components.recipepicker', array(
			'recipes' => $recipes,
			'isRequired' => false,
			'hint' => $__t('This is for statistical purposes only')
			))
			@endif

			<button id="save-consume-button"
				class="btn btn-success">{{ $__t('OK') }}</button>

			@if(GROCY_FEATURE_FLAG_STOCK_PRODUCT_OPENED_TRACKING)
			<button id="save-mark-as-open-button"
				class="btn btn-secondary permission-STOCK_OPEN">{{ $__t('Mark as opened') }}</button>
			@endif

		</form>
	</div>

	<div class="w-full md:w-1/2 xl:w-1/3 md:pl-4 hide-when-embedded">
		@include('components.productcard')
	</div>
</div>
@stop
