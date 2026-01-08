@php require_frontend_packages(['bootstrap-combobox']); @endphp

@once
@push('componentScripts')
<script src="{{ $U('/viewjs/components/recipepicker.js', true) }}?v={{ $version }}"></script>
@endpush
@endonce

@php if(empty($prefillByName)) { $prefillByName = ''; } @endphp
@php if(empty($prefillById)) { $prefillById = ''; } @endphp
@php if(!isset($isRequired)) { $isRequired = true; } @endphp
@php if(empty($hint)) { $hint = ''; } @endphp
@php if(empty($nextInputSelector)) { $nextInputSelector = ''; } @endphp

<div class="mb-4"
	data-next-input-selector="{{ $nextInputSelector }}"
	data-prefill-by-name="{{ $prefillByName }}"
	data-prefill-by-id="{{ $prefillById }}">
	<label class="w-full flex items-center justify-between text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
		for="recipe_id">
		<span>
			{{ $__t('Recipe') }}
			@if(!empty($hint))
			<i class="fa-solid fa-question-circle text-gray-500 dark:text-gray-400"
				data-tooltip
				data-trigger="hover click"
				title="{{ $hint }}"></i>
			@endif
		</span>
		<i class="fa-solid fa-barcode"></i>
	</label>
	<select class="select w-full recipe-combobox barcodescanner-input"
		id="recipe_id"
		name="recipe_id"
		data-target="@recipepicker"
		@if($isRequired)
		required
		@endif>
		<option value=""></option>
		@foreach($recipes as $recipe)
		<option value="{{ $recipe->id }}">{{ $recipe->name }}</option>
		@endforeach
	</select>
	<div class="text-sm text-danger mt-1 hidden invalid-feedback">{{ $__t('You have to select a recipe') }}</div>
</div>

@include('components.camerabarcodescanner')
