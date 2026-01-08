@php require_frontend_packages(['datatables']); @endphp

@extends('layout.default')

@section('title', $__t('Locations'))

@section('content')
<div class="flex flex-wrap">
	<div class="w-full">
		<div class="mb-4">
			<div class="flex flex-wrap items-center justify-between gap-4">
				<h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">@yield('title')</h2>
				<div class="flex gap-2 @if($embedded) pr-5 @endif">
					<button class="btn-secondary md:hidden"
						type="button"
						x-data
						@click="$el.nextElementSibling?.nextElementSibling?.classList.toggle('hidden')">
						<i class="fa-solid fa-filter"></i>
					</button>
					<button class="btn-secondary md:hidden"
						type="button"
						x-data
						@click="document.getElementById('related-links')?.classList.toggle('hidden')">
						<i class="fa-solid fa-ellipsis-v"></i>
					</button>
				</div>
			</div>
			<div class="hidden md:flex flex-wrap gap-2 mt-4"
				id="related-links">
				<a class="btn-primary show-as-dialog-link"
					href="{{ $U('/location/new?embedded') }}">
					{{ $__t('Add') }}
				</a>
				<a class="btn-secondary"
					href="{{ $U('/userfields?entity=locations') }}">
					{{ $__t('Configure userfields') }}
				</a>
			</div>
		</div>
	</div>
</div>

<hr class="my-4 border-gray-200 dark:border-gray-700">

<div class="hidden md:flex flex-wrap gap-4 mb-4"
	id="table-filter-row">
	<div class="w-full md:w-auto md:flex-1 xl:max-w-xs">
		<div class="flex">
			<span class="inline-flex items-center px-3 border border-r-0 border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-400 rounded-l">
				<i class="fa-solid fa-search"></i>
			</span>
			<input type="text"
				id="search"
				class="input rounded-l-none"
				placeholder="{{ $__t('Search') }}">
		</div>
	</div>
	<div class="w-full md:w-auto md:flex-1 xl:max-w-xs">
		<div class="flex items-center gap-2">
			<input class="w-4 h-4 text-primary-500 bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-600 rounded focus:ring-primary-500"
				type="checkbox"
				id="show-disabled">
			<label class="text-sm text-gray-700 dark:text-gray-300"
				for="show-disabled">
				{{ $__t('Show disabled') }}
			</label>
		</div>
	</div>
	<div class="w-full md:w-auto ml-auto">
		<div class="flex justify-end">
			<button id="clear-filter-button"
				class="btn-secondary text-sm"
				data-tooltip="{{ $__t('Clear filter') }}">
				<i class="fa-solid fa-filter-circle-xmark"></i>
			</button>
		</div>
	</div>
</div>

<div class="flex flex-wrap">
	<div class="w-full">
		<table id="locations-table"
			class="w-full text-sm">
			<thead>
				<tr>
					<th class="border-r border-gray-300 dark:border-gray-600">
						<a class="text-gray-500 dark:text-gray-400 change-table-columns-visibility-button"
							data-tooltip="{{ $__t('Table options') }}"
							data-table-selector="#locations-table"
							href="#">
							<i class="fa-solid fa-eye"></i>
						</a>
					</th>
					<th>{{ $__t('Name') }}</th>
					<th>{{ $__t('Description') }}</th>

					@include('components.userfields_thead', array(
					'userfields' => $userfields
					))

				</tr>
			</thead>
			<tbody class="hidden">
				@foreach($locations as $location)
				<tr class="@if($location->active == 0) text-gray-400 dark:text-gray-500 @endif">
					<td class="w-auto border-r border-gray-300 dark:border-gray-600">
						<div class="flex gap-1">
							<a class="btn-primary text-sm px-2 py-1 show-as-dialog-link"
								href="{{ $U('/location/') }}{{ $location->id }}?embedded"
								data-tooltip="{{ $__t('Edit this item') }}">
								<i class="fa-solid fa-edit"></i>
							</a>
							<a class="btn-danger text-sm px-2 py-1 location-delete-button"
								href="#"
								data-location-id="{{ $location->id }}"
								data-location-name="{{ $location->name }}"
								data-tooltip="{{ $__t('Delete this item') }}">
								<i class="fa-solid fa-trash"></i>
							</a>
						</div>
					</td>
					<td>
						{{ $location->name }}
					</td>
					<td>
						{{ $location->description }}
					</td>

					@include('components.userfields_tbody', array(
					'userfields' => $userfields,
					'userfieldValues' => FindAllObjectsInArrayByPropertyValue($userfieldValues, 'object_id', $location->id)
					))

				</tr>
				@endforeach
			</tbody>
		</table>
	</div>
</div>
@stop
