@extends('layout.default')

@if($mode == 'edit')
@section('title', $__t('Edit QU conversion'))
@else
@section('title', $__t('Create QU conversion'))
@endif

@section('content')
<div class="flex flex-wrap">
	<div class="w-full">
		<div class="mb-4">
			<h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
				@yield('title')
			</h2>
			<div class="mt-1">
				@if($product != null)
				<span class="text-sm text-gray-500 dark:text-gray-400">{{ $__t('Override for product') }} <strong>{{ $product->name }}</strong></span>
				@else
				<span class="text-sm text-gray-500 dark:text-gray-400">{{ $__t('Default for QU') }} <strong>{{ $defaultQuUnit->name }}</strong></span>
				@endif
			</div>
		</div>
	</div>
</div>

<hr class="my-4 border-gray-200 dark:border-gray-700">

<div class="flex flex-wrap">
	<div class="w-full lg:w-1/2">

		<script>
			Grocy.EditMode = '{{ $mode }}';
		</script>

		@if($mode == 'edit')
		<script>
			Grocy.EditObjectId = {{ $quConversion->id }};
		</script>
		@endif

		<form id="quconversion-form"
			novalidate>

			@if($product != null)
			<input type="hidden"
				name="product_id"
				value="{{ $product->id }}">
			@endif

			<div class="mb-4">
				<label for="from_qu_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
					{{ $__t('Quantity unit from') }}
				</label>
				<select required
					class="select w-full input-group-qu"
					id="from_qu_id"
					name="from_qu_id">
					<option></option>
					@foreach($quantityunits as $quantityunit)
					@php
					$selected = false;
					if ($mode == 'edit')
					{
					if ($quantityunit->id == $quConversion->from_qu_id)
					{
					$selected = true;
					}
					}
					else
					{
					if ($product != null && $quantityunit->id == $product->qu_id_stock)
					{
					$selected = true;
					}
					else
					{
					if ($quantityunit->id == $defaultQuUnit->id)
					{
					$selected = true;
					}
					}
					}
					@endphp
					<option @if($selected)
						selected="selected"
						@endif
						value="{{ $quantityunit->id }}"
						data-plural-form="{{ $quantityunit->name_plural }}">{{ $quantityunit->name }}</option>
					@endforeach
				</select>
				<div class="text-sm text-red-600 dark:text-red-400 mt-1 hidden invalid-feedback">
					{{ $__t('A quantity unit is required') }}
				</div>
			</div>

			<div class="mb-4">
				<label for="to_qu_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
					{{ $__t('Quantity unit to') }}
				</label>
				<select required
					class="select w-full input-group-qu"
					id="to_qu_id"
					name="to_qu_id">
					<option></option>
					@foreach($quantityunits as $quantityunit)
					<option @if($mode=='edit'
						&&
						$quantityunit->id == $quConversion->to_qu_id) selected="selected" @endif value="{{ $quantityunit->id }}" data-plural-form="{{ $quantityunit->name_plural }}">{{ $quantityunit->name }}</option>
					@endforeach
				</select>
				<div class="text-sm text-red-600 dark:text-red-400 mt-1 hidden invalid-feedback">
					{{ $__t('A quantity unit is required') }}
				</div>
			</div>

			@php if($mode == 'edit') { $value = $quConversion->factor; } else { $value = 1; } @endphp
			@include('components.numberpicker', array(
			'id' => 'factor',
			'label' => 'Factor',
			'min' => $DEFAULT_MIN_AMOUNT,
			'decimals' => $userSettings['stock_decimal_places_amounts'],
			'value' => $value,
			'additionalHtmlElements' => '<p id="qu-conversion-info"
				class="text-sm text-info-DEFAULT dark:text-info-light mt-1 hidden mb-0"></p>
			<p id="qu-conversion-inverse-info"
				class="text-sm text-info-DEFAULT dark:text-info-light mt-1 hidden"></p>',
			'additionalCssClasses' => 'input-group-qu locale-number-input locale-number-quantity-amount'
			))

			@include('components.userfieldsform', array(
			'userfields' => $userfields,
			'entity' => 'quantity_unit_conversions'
			))

			<button id="save-quconversion-button"
				class="btn-primary">{{ $__t('Save') }}</button>

		</form>
	</div>
</div>
@stop
