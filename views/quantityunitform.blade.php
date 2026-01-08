@php require_frontend_packages(['datatables']); @endphp

@extends('layout.default')

@if($mode == 'edit')
@section('title', $__t('Edit quantity unit'))
@else
@section('title', $__t('Create quantity unit'))
@endif

@section('content')
<div class="flex flex-wrap">
	<div class="w-full">
		<h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">@yield('title')</h2>
	</div>
</div>

<hr class="my-4 border-gray-200 dark:border-gray-700">

<div class="flex flex-wrap gap-8">
	<div class="w-full lg:w-[calc(50%-1rem)]">
		<script>
			Grocy.EditMode = '{{ $mode }}';
		</script>

		@if($mode == 'edit')
		<script>
			Grocy.EditObjectId = {{ $quantityUnit->id }};
		</script>
		@endif

		<form id="quantityunit-form"
			novalidate>

			<div class="mb-4">
				<label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
					{{ $__t('Name') }} <span class="text-xs text-gray-500 dark:text-gray-400">{{ $__t('in singular form') }}</span>
				</label>
				<input type="text"
					class="input w-full"
					required
					id="name"
					name="name"
					value="@if($mode == 'edit'){{ $quantityUnit->name }}@endif">
				<div class="text-sm text-red-600 dark:text-red-400 mt-1 hidden invalid-feedback">
					{{ $__t('A name is required') }}
				</div>
			</div>

			<div class="mb-4">
				<label for="name_plural" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
					{{ $__t('Name') }} <span class="text-xs text-gray-500 dark:text-gray-400">{{ $__t('in plural form') }}</span>
				</label>
				<input type="text"
					class="input w-full"
					id="name_plural"
					name="name_plural"
					value="@if($mode == 'edit'){{ $quantityUnit->name_plural }}@endif">
			</div>

			@if($pluralCount > 2)
			<div class="mb-4">
				<label for="plural_forms" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
					{{ $__t('Plural forms') }}<br>
					<span class="text-xs text-gray-500 dark:text-gray-400">
						{{ $__t('One plural form per line, the current language requires') }}:<br>
						{{ $__t('Plural count') }}: {{ $pluralCount }}<br>
						{{ $__t('Plural rule') }}: {{ $pluralRule }}
					</span>
				</label>
				<textarea class="input w-full"
					rows="3"
					id="plural_forms"
					name="plural_forms">@if($mode == 'edit'){{ $quantityUnit->plural_forms }}@endif</textarea>
			</div>
			@endif

			<div class="mb-4">
				<div class="flex items-center gap-2">
					<input @if($mode=='create'
						)
						checked
						@elseif($mode=='edit'
						&&
						$quantityUnit->active == 1) checked @endif class="w-4 h-4 text-primary-500 bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-600 rounded focus:ring-primary-500" type="checkbox" id="active" name="active" value="1">
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
					name="description">@if($mode == 'edit'){{ $quantityUnit->description }}@endif</textarea>
			</div>

			@include('components.userfieldsform', array(
			'userfields' => $userfields,
			'entity' => 'quantity_units'
			))

			<p class="my-2 text-sm text-gray-500 dark:text-gray-400 @if($mode == 'edit') hidden @endif">
				{{ $__t('Save & continue to add conversions') }}
			</p>

			<div class="flex flex-wrap gap-2">
				<button class="save-quantityunit-button btn-primary mb-2"
					data-location="continue">{{ $__t('Save & continue') }}</button>
				<button class="save-quantityunit-button btn-secondary mb-2"
					data-location="return">{{ $__t('Save & return to quantity units') }}</button>

				@if($pluralCount > 2)
				<button id="test-quantityunit-plural-forms-button"
					class="btn-secondary mb-2">{{ $__t('Test plural forms') }}</button>
				@endif
			</div>

		</form>
	</div>

	<div class="w-full lg:w-[calc(50%-1rem)] @if($mode == 'create') hidden @endif">
		<div class="flex flex-wrap">
			<div class="w-full">
				<div class="mb-4">
					<div class="flex flex-wrap items-center justify-between gap-4">
						<h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
							{{ $__t('Default conversions') }}
							<small id="qu-conversion-headline-info"
								class="text-sm text-gray-500 dark:text-gray-400 italic ml-2"></small>
						</h4>
						<button class="btn-secondary md:hidden"
							type="button"
							x-data
							@click="document.getElementById('related-links')?.classList.toggle('hidden')">
							<i class="fa-solid fa-ellipsis-v"></i>
						</button>
					</div>
					<div class="hidden md:flex flex-wrap gap-2 mt-2"
						id="related-links">
						<a class="btn-primary text-sm show-as-dialog-link"
							href="{{ $U('/quantityunitconversion/new?embedded&qu-unit=' . $quantityUnit->id ) }}">
							{{ $__t('Add') }}
						</a>
					</div>
				</div>

				<table id="qu-conversions-table"
					class="w-full text-sm">
					<thead>
						<tr>
							<th class="border-r border-gray-300 dark:border-gray-600">
								<a class="text-gray-500 dark:text-gray-400 change-table-columns-visibility-button"
									data-tooltip="{{ $__t('Table options') }}"
									data-table-selector="#qu-conversions-table"
									href="#">
									<i class="fa-solid fa-eye"></i>
								</a>
							</th>
							<th>{{ $__t('Factor') }}</th>
							<th>{{ $__t('Unit') }}</th>
						</tr>
					</thead>
					<tbody class="hidden">
						@if($mode == "edit")
						@foreach($defaultQuConversions as $defaultQuConversion)
						<tr>
							<td class="w-auto border-r border-gray-300 dark:border-gray-600">
								<div class="flex gap-1">
									<a class="btn-primary text-sm px-2 py-1 show-as-dialog-link"
										href="{{ $U('/quantityunitconversion/' . $defaultQuConversion->id . '?embedded&qu-unit=' . $quantityUnit->id ) }}"
										data-qu-conversion-id="{{ $defaultQuConversion->id }}">
										<i class="fa-solid fa-edit"></i>
									</a>
									<a class="btn-danger text-sm px-2 py-1 qu-conversion-delete-button"
										href="#"
										data-qu-conversion-id="{{ $defaultQuConversion->id }}">
										<i class="fa-solid fa-trash"></i>
									</a>
								</div>
							</td>
							<td>
								<span class="locale-number locale-number-quantity-amount">{{ $defaultQuConversion->factor }}</span>
							</td>
							<td>
								{{ FindObjectInArrayByPropertyValue($quantityUnits, 'id', $defaultQuConversion->to_qu_id)->name }}
							</td>
						</tr>
						@endforeach
						@endif
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
@stop
