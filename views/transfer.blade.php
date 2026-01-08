@extends('layout.default')

@section('title', $__t('Transfer'))

@section('content')
<script>
	Grocy.QuantityUnits = {!! json_encode($quantityUnits) !!};
	Grocy.QuantityUnitConversionsResolved = {!! json_encode($quantityUnitConversionsResolved) !!};
</script>

<div class="flex flex-wrap">
	<div class="w-full md:w-1/2 xl:w-1/3 pb-3">
		<h2 class="text-xl font-bold text-gray-900 dark:text-gray-100">@yield('title')</h2>

		<hr class="my-2 border-gray-200 dark:border-gray-700">

		<form id="transfer-form"
			novalidate>

			@include('components.productpicker', array(
			'products' => $products,
			'barcodes' => $barcodes,
			'nextInputSelector' => '#location_id_from',
			'disallowAddProductWorkflows' => true
			))

			<div class="form-group">
				<label class="form-label" for="location_id_from">{{ $__t('From location') }}</label>
				<select required
					class="select location-combobox"
					id="location_id_from"
					name="location_id_from">
					<option></option>
					@foreach($locations as $location)
					<option value="{{ $location->id }}"
						data-is-freezer="{{ $location->is_freezer }}">{{ $location->name }}</option>
					@endforeach
				</select>
				<div class="form-error hidden">{{ $__t('A location is required') }}</div>
			</div>

			@include('components.productamountpicker', array(
			'value' => 1,
			'additionalHtmlContextHelp' => '<div id="tare-weight-handling-info"
				class="text-blue-600 dark:text-blue-400 italic hidden">' . $__t('Tare weight handling enabled - please weigh the whole container, the amount to be posted will be automatically calculcated') . '</div>'
			))

			<div class="form-group">
				<label class="form-label" for="location_id_to">{{ $__t('To location') }}</label>
				<select required
					class="select location-combobox"
					id="location_id_to"
					name="location_id_to">
					<option></option>
					@foreach($locations as $location)
					<option value="{{ $location->id }}"
						data-is-freezer="{{ $location->is_freezer }}">{{ $location->name }}</option>
					@endforeach
				</select>
				<div class="form-error hidden">{{ $__t('A location is required') }}</div>
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

			<button id="save-transfer-button"
				class="btn btn-success">{{ $__t('OK') }}</button>

		</form>
	</div>

	<div class="w-full md:w-1/2 xl:w-1/3 md:pl-4 hide-when-embedded">
		@include('components.productcard')
	</div>
</div>
@stop
