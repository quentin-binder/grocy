@extends('layout.default')

@section('title', $__t('Inventory'))

@section('content')
<script>
	Grocy.QuantityUnits = {!! json_encode($quantityUnits) !!};
	Grocy.QuantityUnitConversionsResolved = {!! json_encode($quantityUnitConversionsResolved) !!};
	Grocy.DefaultMinAmount = '{{$DEFAULT_MIN_AMOUNT}}';
</script>

<div class="flex flex-wrap">
	<div class="w-full md:w-1/2 xl:w-1/3 pb-3">
		<h2 class="title">@yield('title')</h2>

		<hr class="my-2 border-t border-gray-200 dark:border-gray-700">

		<form id="inventory-form"
			novalidate>

			@include('components.productpicker', array(
			'products' => $products,
			'barcodes' => $barcodes,
			'nextInputSelector' => '#new_amount'
			))

			@include('components.productamountpicker', array(
			'value' => 1,
			'label' => 'New stock amount',
			'additionalHtmlElements' => '<div id="inventory-change-info"
				class="text-sm text-gray-500 dark:text-gray-400 hidden ml-3 my-0 w-full"></div>',
			'additionalHtmlContextHelp' => '<div id="tare-weight-handling-info"
				class="text-info italic hidden">' . $__t('Tare weight handling enabled - please weigh the whole container, the amount to be posted will be automatically calculcated') . '</div>'
			))

			@if(boolval($userSettings['show_purchased_date_on_purchase']))
			@include('components.datetimepicker2', array(
			'id' => 'purchased_date',
			'label' => 'Purchased date',
			'format' => 'YYYY-MM-DD',
			'hint' => $__t('This will apply to added products'),
			'initWithNow' => true,
			'limitEndToNow' => false,
			'limitStartToNow' => false,
			'invalidFeedback' => $__t('A purchased date is required'),
			'nextInputSelector' => '#best_before_date',
			'additionalCssClasses' => 'date-only-datetimepicker2',
			'activateNumberPad' => GROCY_FEATURE_FLAG_STOCK_BEST_BEFORE_DATE_FIELD_NUMBER_PAD
			))
			@endif

			@php
			$additionalGroupCssClasses = '';
			if (!GROCY_FEATURE_FLAG_STOCK_BEST_BEFORE_DATE_TRACKING)
			{
			$additionalGroupCssClasses = 'hidden';
			}
			@endphp
			@include('components.datetimepicker', array(
			'id' => 'best_before_date',
			'label' => 'Due date',
			'hint' => $__t('This will apply to added products'),
			'format' => 'YYYY-MM-DD',
			'initWithNow' => false,
			'limitEndToNow' => false,
			'limitStartToNow' => false,
			'invalidFeedback' => $__t('A due date is required'),
			'nextInputSelector' => '#best_before_date',
			'additionalGroupCssClasses' => 'date-only-datetimepicker',
			'shortcutValue' => '2999-12-31',
			'shortcutLabel' => 'Never overdue',
			'earlierThanInfoLimit' => date('Y-m-d'),
			'earlierThanInfoText' => $__t('The given date is earlier than today, are you sure?'),
			'additionalGroupCssClasses' => $additionalGroupCssClasses,
			'activateNumberPad' => GROCY_FEATURE_FLAG_STOCK_BEST_BEFORE_DATE_FIELD_NUMBER_PAD
			))
			@php $additionalGroupCssClasses = ''; @endphp

			@if(GROCY_FEATURE_FLAG_STOCK_PRICE_TRACKING)
			@include('components.numberpicker', array(
			'id' => 'price',
			'label' => 'Price',
			'min' => '0.' . str_repeat('0', $userSettings['stock_decimal_places_prices_input']),
			'decimals' => $userSettings['stock_decimal_places_prices_input'],
			'value' => '',
			'contextInfoId' => 'price-hint',
			'hint' => $__t('This will apply to added products'),
			'isRequired' => false,
			'additionalCssClasses' => 'locale-number-input locale-number-currency'
			))

			@include('components.shoppinglocationpicker', array(
			'label' => 'Store',
			'shoppinglocations' => $shoppinglocations,
			'hint' => $__t('This will apply to added products'),
			))
			@else
			<input type="hidden"
				name="price"
				id="price"
				value="0">
			@endif

			@if(GROCY_FEATURE_FLAG_STOCK_LOCATION_TRACKING)
			@include('components.locationpicker', array(
			'locations' => $locations,
			'hint' => $__t('This will apply to added products')
			))
			@endif

			@if(GROCY_FEATURE_FLAG_LABEL_PRINTER)
			<div class="mb-4">
				<label for="stock_label_type" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
					{{ $__t('Stock entry label') }}
					<i class="fa-solid fa-question-circle text-gray-400"
						data-tooltip="{{ $__t('This will apply to added products') }}"></i>
				</label>
				<select class="select w-full"
					id="stock_label_type"
					name="stock_label_type">
					<option value="0">{{ $__t('No label') }}</option>
					<option value="1">{{ $__t('Single label') }}</option>
					<option value="2">{{ $__t('Label per unit') }}</option>
				</select>
				<div id="stock-entry-label-info"
					class="text-sm text-info mt-1"></div>
			</div>
			@endif

			<div class="mb-4">
				<label for="note" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
					{{ $__t('Note') }}
					<i class="fa-solid fa-question-circle text-gray-400"
						data-tooltip="{{ $__t('This will apply to added products') }}"></i>
				</label>
				<input type="text"
					class="input w-full"
					id="note"
					name="note">
			</div>

			@include('components.userfieldsform', array(
			'userfields' => $userfields,
			'entity' => 'stock'
			))

			<button id="save-inventory-button"
				class="btn-primary">{{ $__t('OK') }}</button>

		</form>
	</div>

	<div class="w-full md:w-1/2 xl:w-1/3 hide-when-embedded">
		@include('components.productcard')
	</div>
</div>
@stop
