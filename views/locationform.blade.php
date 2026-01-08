@extends('layout.default')

@if($mode == 'edit')
@section('title', $__t('Edit location'))
@else
@section('title', $__t('Create location'))
@endif

@section('content')
<div class="flex flex-wrap">
	<div class="w-full">
		<h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">@yield('title')</h2>
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
			Grocy.EditObjectId = {{ $location->id }};
		</script>
		@endif

		<form id="location-form"
			novalidate>

			<div class="mb-4">
				<label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
					{{ $__t('Name') }}
				</label>
				<input type="text"
					class="input w-full"
					required
					id="name"
					name="name"
					value="@if($mode == 'edit'){{ $location->name }}@endif">
				<div class="text-sm text-red-600 dark:text-red-400 mt-1 hidden invalid-feedback">
					{{ $__t('A name is required') }}
				</div>
			</div>

			<div class="mb-4">
				<div class="flex items-center gap-2">
					<input @if($mode=='create'
						)
						checked
						@elseif($mode=='edit'
						&&
						$location->active == 1) checked @endif class="w-4 h-4 text-primary-500 bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-600 rounded focus:ring-primary-500" type="checkbox" id="active" name="active" value="1">
					<label class="text-sm text-gray-700 dark:text-gray-300"
						for="active">{{ $__t('Active') }}</label>
				</div>
			</div>

			<div class="mb-4">
				<label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
					{{ $__t('Description') }}
				</label>
				<textarea class="input w-full"
					rows="2"
					id="description"
					name="description">@if($mode == 'edit'){{ $location->description }}@endif</textarea>
			</div>

			@if(GROCY_FEATURE_FLAG_STOCK_PRODUCT_FREEZING)
			<div class="mb-4">
				<div class="flex items-center gap-2">
					<input @if($mode=='edit'
						&&
						$location->is_freezer == 1) checked @endif class="w-4 h-4 text-primary-500 bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-600 rounded focus:ring-primary-500" type="checkbox" id="is_freezer" name="is_freezer" value="1">
					<label class="text-sm text-gray-700 dark:text-gray-300"
						for="is_freezer">{{ $__t('Is freezer') }}
						<i class="fa-solid fa-question-circle text-gray-500 dark:text-gray-400 ml-1"
							data-tooltip="{{ $__t('When moving products from/to a freezer location, the products due date is automatically adjusted according to the product settings') }}"></i>
					</label>
				</div>
			</div>
			@else
			<input type="hidden"
				name="is_freezer"
				value="0">
			@endif

			@include('components.userfieldsform', array(
			'userfields' => $userfields,
			'entity' => 'locations'
			))

			<button id="save-location-button"
				class="btn-primary">{{ $__t('Save') }}</button>

		</form>
	</div>
</div>
@stop
