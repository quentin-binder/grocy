@once
@push('componentScripts')
<script src="{{ $U('/viewjs/components/productamountpicker.js', true) }}?v={{ $version }}"></script>
@endpush
@endonce

@php if(empty($additionalGroupCssClasses)) { $additionalGroupCssClasses = ''; } @endphp
@php if(empty($additionalHtmlContextHelp)) { $additionalHtmlContextHelp = ''; } @endphp
@php if(empty($additionalHtmlElements)) { $additionalHtmlElements = ''; } @endphp
@php if(empty($label)) { $label = 'Amount'; } @endphp
@php if(empty($initialQuId)) { $initialQuId = '-1'; } @endphp
@php if(!isset($isRequired)) { $isRequired = true; } @endphp
@php if(!isset($allowZero)) { $allowZero = false; } @endphp

@php
$minLocal = $DEFAULT_MIN_AMOUNT;
if ($allowZero)
{
$minLocal = 0;
}
@endphp

<div class="mb-4 {{ $additionalGroupCssClasses }}">
	<div class="w-full">
		{!! $additionalHtmlContextHelp !!}

		<div class="flex flex-wrap gap-4">

			@include('components.numberpicker', array(
			'id' => 'display_amount',
			'label' => $label,
			'min' => $minLocal,
			'decimals' => $userSettings['stock_decimal_places_amounts'],
			'value' => $value,
			'additionalGroupCssClasses' => 'w-full sm:w-5/12 my-0',
			'additionalCssClasses' => 'input-group-productamountpicker locale-number-input locale-number-quantity-amount',
			'additionalHtmlContextHelp' => '',
			'additionalHtmlElements' => ''
			))

			<div class="w-full sm:w-7/12">
				<label for="qu_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ $__t('Quantity unit') }}</label>
				<select @if($isRequired)
					required
					@endif
					class="select w-full input-group-productamountpicker"
					id="qu_id"
					name="qu_id"
					data-initial-qu-id="{{ $initialQuId }}">
					<option></option>
				</select>
				<div class="text-sm text-danger mt-1 hidden invalid-feedback">{{ $__t('A quantity unit is required') }}</div>
			</div>

			<div id="qu-conversion-info"
				class="ml-3 my-0 text-sm text-info hidden w-full"></div>

			{!! $additionalHtmlElements !!}

			<input type="hidden"
				id="amount"
				name="amount"
				value="">

		</div>
	</div>
</div>
