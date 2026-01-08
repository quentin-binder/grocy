@php require_frontend_packages(['datatables', 'summernote', 'animatecss', 'bwipjs']); @endphp

@extends('layout.default')

@section('title', $__t('Shopping list'))

@push('pageStyles')
<style>
	#shopping-list-print-shadow-table_wrapper .dataTable>thead>tr>th[class*="sort"]:before,
	#shopping-list-print-shadow-table_wrapper .dataTable>thead>tr>th[class*="sort"]:after {
		content: "" !important;
	}
</style>
@endpush

@push('pageScripts')
<script src="{{ $U('/viewjs/purchase.js?v=', true) }}{{ $version }}"></script>
@endpush

@php
if(boolval($userSettings['shopping_list_round_up']))
{
foreach($listItems as $listItem)
{
$listItem->amount = ceil($listItem->amount);
$listItem->last_price_total = $listItem->price * $listItem->amount;
}
}
@endphp

@section('content')
<div class="flex flex-wrap print:hidden hide-on-fullscreen-card">
	<div class="w-full">
		<div class="flex flex-wrap items-center gap-2">
			<h2 class="text-xl font-bold text-gray-900 dark:text-gray-100 mr-2">
				@yield('title')
			</h2>
			@if(GROCY_FEATURE_FLAG_STOCK_PRICE_TRACKING)
			<h2 class="mb-0 mr-auto w-full md:w-auto order-3 md:order-1">
				<span class="text-gray-500 dark:text-gray-400 text-sm">{!! $__t('%s total value', '<span class="locale-number locale-number-currency">' . SumArrayValue($listItems, 'last_price_total') . '</span>') !!}</span>
			</h2>
			@endif
			<div class="float-right @if($embedded) pr-5 @endif">
				<button class="btn btn-primary md:hidden mt-2 order-1 md:order-3 show-as-dialog-link"
					href="{{ $U('/shoppinglistitem/new?embedded&list=' . $selectedShoppingListId) }}">
					{{ $__t('Add item') }}
				</button>
				<button class="btn btn-secondary md:hidden mt-2 order-1 md:order-3"
					type="button"
					data-toggle="collapse"
					data-target="#table-filter-row">
					<i class="fa-solid fa-filter"></i>
				</button>
				<button class="btn btn-secondary md:hidden mt-2 order-1 md:order-3"
					type="button"
					data-toggle="collapse"
					data-target="#related-links">
					<i class="fa-solid fa-ellipsis-v"></i>
				</button>
			</div>
			<div class="related-links collapse md:flex order-2 w-full md:w-auto"
				id="related-links">
				@if(GROCY_FEATURE_FLAG_SHOPPINGLIST_MULTIPLE_LISTS)
				<div class="my-auto float-right">
					<select class="select bg-gray-100 dark:bg-gray-700 font-bold md:mt-0 mt-1"
						id="selected-shopping-list">
						@foreach($shoppingLists as $shoppingList)
						<option @if($shoppingList->id == $selectedShoppingListId) selected="selected" @endif value="{{ $shoppingList->id }}" data-shoppinglist-name="{{ $shoppingList->name }}">{{ $shoppingList->name }} ({{ $shoppingList->item_count }})</option>
						@endforeach
					</select>
				</div>
				<div class="dropdown inline-block"
					x-data="{ open: false }">
					<a class="btn btn-secondary m-1 md:mt-0 md:mb-0 float-right inline-flex items-center gap-1"
						href="#"
						@click.prevent="open = !open">
						{{ $__t('List actions') }}
						<i class="fa-solid fa-chevron-down text-xs"></i>
					</a>
					<div class="dropdown-menu absolute bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow-lg py-1 mt-1 min-w-40 z-50"
						x-show="open"
						@click.outside="open = false"
						x-cloak>
						<a class="dropdown-item show-as-dialog-link"
							href="{{ $U('/shoppinglist/new?embedded') }}">
							{{ $__t('New shopping list') }}
						</a>
						<a class="dropdown-item show-as-dialog-link"
							href="{{ $U('/shoppinglist/' . $selectedShoppingListId . '?embedded') }}">
							{{ $__t('Edit shopping list') }}
						</a>
						<a id="delete-selected-shopping-list"
							class="dropdown-item text-red-600 dark:text-red-400 @if($selectedShoppingListId == 1) opacity-50 pointer-events-none @endif"
							href="#">
							{{ $__t('Delete shopping list') }}
						</a>
						<div class="border-t border-gray-200 dark:border-gray-700 my-1"></div>
						<a id="print-shopping-list-button"
							class="dropdown-item"
							href="#">
							{{ $__t('Print') }}
						</a>
					</div>
				</div>
				@else
				<input type="hidden"
					name="selected-shopping-list"
					id="selected-shopping-list"
					value="1">
				@endif
			</div>
		</div>
		<div id="filter-container"
			class="border-t border-b border-gray-200 dark:border-gray-700 my-2 py-1">
			<div id="table-filter-row"
				data-status-filter="belowminstockamount"
				class="collapse normal-message status-filter-message responsive-button @if(!GROCY_FEATURE_FLAG_STOCK) hidden @else md:inline-block @endif"><span class="block md:hidden">{{count($missingProducts)}} <i class="fa-solid fa-exclamation-circle"></i></span><span class="hidden md:block">{{ $__n(count($missingProducts), '%s product is below defined min. stock amount', '%s products are below defined min. stock amount') }}</span></div>
			<div id="related-links"
				class="float-right mt-1 collapse md:block">
				<a class="btn btn-primary btn-sm mb-1 show-as-dialog-link hidden md:inline-block"
					href="{{ $U('/shoppinglistitem/new?embedded&list=' . $selectedShoppingListId) }}">
					{{ $__t('Add item') }}
				</a>
				<div class="inline-flex gap-1">
					<a id="clear-shopping-list"
						class="btn btn-sm text-red-600 dark:text-red-400 border border-red-600 dark:border-red-400 mb-1 @if($listItems->count() == 0) opacity-50 pointer-events-none @endif"
						href="#">
						{{ $__t('Clear list') }}
					</a>
					<a id="clear-done-items"
						class="btn btn-sm text-red-600 dark:text-red-400 border border-red-600 dark:border-red-400 mb-1 @if($listItems->count() == 0) opacity-50 pointer-events-none @endif"
						href="#">
						{{ $__t('Clear done items') }}
					</a>
				</div>

				@if(GROCY_FEATURE_FLAG_STOCK)
				<div class="dropdown inline-block"
					x-data="{ open: false }">
					<a class="btn btn-sm btn-secondary mb-1 inline-flex items-center gap-1"
						href="#"
						@click.prevent="open = !open">
						{{ $__t('Stock actions') }}
						<i class="fa-solid fa-chevron-down text-xs"></i>
					</a>
					<div class="dropdown-menu absolute right-0 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow-lg py-1 mt-1 min-w-48 z-50 text-right"
						x-show="open"
						@click.outside="open = false"
						x-cloak>
						<a id="add-all-items-to-stock-button"
							class="dropdown-item"
							href="#">{{ $__t('Add all list items to stock') }}</a>
						@if(!boolval($userSettings['shopping_list_auto_add_below_min_stock_amount']))
						<a id="add-products-below-min-stock-amount"
							class="dropdown-item"
							href="#">{{ $__t('Add products that are below defined min. stock amount') }}</a>
						@endif
						<a id="add-overdue-expired-products"
							class="dropdown-item"
							href="#">{{ $__t('Add overdue/expired products') }}</a>
					</div>
				</div>
				@endif
			</div>
		</div>
	</div>
</div>

<div class="flex flex-wrap collapse md:flex print:hidden hide-on-fullscreen-card"
	id="table-filter-row">
	<div class="w-full md:w-1/2 xl:w-1/4 px-2 mb-2">
		<div class="input-group flex">
			<span class="input-group-text rounded-l-md border-r-0"><i class="fa-solid fa-search"></i></span>
			<input type="text"
				id="search"
				class="input flex-1 rounded-l-none"
				placeholder="{{ $__t('Search') }}">
		</div>
	</div>
	<div class="w-full md:w-1/2 xl:w-1/4 px-2 mb-2">
		<div class="input-group flex">
			<span class="input-group-text rounded-l-md border-r-0"><i class="fa-solid fa-filter"></i>&nbsp;{{ $__t('Status') }}</span>
			<select class="select flex-1 rounded-l-none"
				id="status-filter">
				<option value="all">{{ $__t('All') }}</option>
				<option class="@if(!GROCY_FEATURE_FLAG_STOCK) hidden @endif"
					value="belowminstockamount">{{ $__t('Below min. stock amount') }}</option>
				<option value="xxDONExx">{{ $__t('Only done items') }}</option>
				<option value="xxUNDONExx">{{ $__t('Only undone items') }}</option>
			</select>
		</div>
	</div>
	<div class="w-full md:w-auto px-2 mb-2 md:ml-auto">
		<button id="clear-filter-button"
			class="btn btn-sm text-blue-600 dark:text-blue-400 border border-blue-600 dark:border-blue-400"
			data-toggle="tooltip"
			title="{{ $__t('Clear filter') }}">
			<i class="fa-solid fa-filter-circle-xmark"></i>
		</button>
	</div>
</div>

<div id="shoppinglist-main"
	class="flex flex-wrap print:hidden">
	<div class="@if(boolval($userSettings['shopping_list_show_calendar'])) w-full md:w-2/3 @else w-full @endif pb-3">
		<table id="shoppinglist-table"
			class="table table-sm table-striped nowrap w-full">
			<thead>
				<tr>
					<th class="border-r border-gray-200 dark:border-gray-700"><a class="text-gray-500 dark:text-gray-400 change-table-columns-visibility-button"
							data-toggle="tooltip"
							title="{{ $__t('Table options') }}"
							data-table-selector="#shoppinglist-table"
							href="#"><i class="fa-solid fa-eye"></i></a>
					</th>
					<th class="allow-grouping">{{ $__t('Product') }} / <em>{{ $__t('Note') }}</em></th>
					<th>{{ $__t('Amount') }}</th>
					<th class="allow-grouping">{{ $__t('Product group') }}</th>
					<th class="hidden">Hidden status</th>
					<th class="@if(!GROCY_FEATURE_FLAG_STOCK_PRICE_TRACKING) hidden @endif">{{ $__t('Last price (Unit)') }}</th>
					<th class="@if(!GROCY_FEATURE_FLAG_STOCK_PRICE_TRACKING) hidden @endif">{{ $__t('Last price (Total)') }}</th>
					<th class="@if(!GROCY_FEATURE_FLAG_STOCK_PRICE_TRACKING) hidden @endif allow-grouping">{{ $__t('Default store') }}</th>
					<th>{{ $__t('Barcodes') }}</th>

					@include('components.userfields_thead', array(
					'userfields' => $userfields
					))
					@include('components.userfields_thead', array(
					'userfields' => $productUserfields
					))

				</tr>
			</thead>
			<tbody class="hidden">
				@foreach($listItems as $listItem)
				<tr id="shoppinglistitem-{{ $listItem->id }}-row"
					class="@if(FindObjectInArrayByPropertyValue($missingProducts, 'id', $listItem->product_id) !== null) bg-blue-100 dark:bg-blue-900/30 @endif @if($listItem->done == 1) text-gray-500 dark:text-gray-400 line-through @endif">
					<td class="fit-content border-r border-gray-200 dark:border-gray-700">
						<a class="btn btn-success btn-sm order-listitem-button"
							href="#"
							data-item-id="{{ $listItem->id }}"
							data-item-done="{{ $listItem->done }}"
							data-toggle="tooltip"
							data-placement="right"
							title="{{ $__t('Mark this item as done') }}">
							<i class="fa-solid fa-check"></i>
						</a>
						<a class="btn btn-sm bg-blue-500 text-white hover:bg-blue-600 show-as-dialog-link"
							href="{{ $U('/shoppinglistitem/' . $listItem->id . '?embedded&list=' . $selectedShoppingListId ) }}"
							data-toggle="tooltip"
							data-placement="right"
							title="{{ $__t('Edit this item') }}">
							<i class="fa-solid fa-edit"></i>
						</a>
						<a class="btn btn-sm btn-danger shoppinglist-delete-button"
							href="#"
							data-shoppinglist-id="{{ $listItem->id }}"
							data-toggle="tooltip"
							data-placement="right"
							title="{{ $__t('Delete this item') }}">
							<i class="fa-solid fa-trash"></i>
						</a>
						<a class="btn btn-sm btn-primary @if(!GROCY_FEATURE_FLAG_STOCK) hidden @endif @if(empty($listItem->product_id)) opacity-50 pointer-events-none @else shopping-list-stock-add-workflow-list-item-button @endif"
							href="{{ $U('/purchase?embedded&flow=shoppinglistitemtostock&product=') }}{{ $listItem->product_id }}&amount={{ $listItem->amount }}&listitemid={{ $listItem->id }}&quId={{ $listItem->qu_id }}"
							@if(!empty($listItem->product_id)) data-toggle="tooltip" title="{{ $__t('Add this item to stock') }}" @endif>
							<i class="fa-solid fa-box"></i>
						</a>
					</td>
					<td class="productcard-trigger cursor-pointer"
						data-product-id="{{ $listItem->product_id }}">
						@if(!empty($listItem->product_id)) {{ $listItem->product_name }}<br>@endif<em>{!! nl2br($listItem->note ?? '') !!}</em>
					</td>
					@if(!empty($listItem->product_id))
					@php
					$listItem->amount_origin_qu = $listItem->amount;
					$product = FindObjectInArrayByPropertyValue($products, 'id', $listItem->product_id);
					$productQuConversions = FindAllObjectsInArrayByPropertyValue($quantityUnitConversionsResolved, 'product_id', $product->id);
					$productQuConversions = FindAllObjectsInArrayByPropertyValue($productQuConversions, 'from_qu_id', $product->qu_id_stock);
					$productQuConversion = FindObjectInArrayByPropertyValue($productQuConversions, 'to_qu_id', $listItem->qu_id);
					if ($productQuConversion)
					{
					$listItem->amount = $listItem->amount * $productQuConversion->factor;
					}
					@endphp
					@endif
					<td>
						<span class="custom-sort hidden">{{$listItem->amount}}</span>
						<span class="locale-number locale-number-quantity-amount">{{ $listItem->amount }}</span> @if(!empty($listItem->product_id)){{ $__n($listItem->amount, $listItem->qu_name, $listItem->qu_name_plural, true) }}@endif
					</td>
					<td>
						@if(!empty($listItem->product_group_name)) {{ $listItem->product_group_name }} @else <span class="italic font-light">{{ $__t('Ungrouped') }}</span> @endif
					</td>
					<td id="shoppinglistitem-{{ $listItem->id }}-status-info"
						class="hidden">
						@if(FindObjectInArrayByPropertyValue($missingProducts, 'id', $listItem->product_id) !== null) belowminstockamount @endif
						@if($listItem->done == 1) xxDONExx @else xxUNDONExx @endif
					</td>
					<td class="@if(!GROCY_FEATURE_FLAG_STOCK_PRICE_TRACKING) hidden @endif">
						<span class="locale-number locale-number-currency">{{ $listItem->last_price_unit }}</span>
					</td>
					<td class="@if(!GROCY_FEATURE_FLAG_STOCK_PRICE_TRACKING) hidden @endif">
						<span class="locale-number locale-number-currency">{{ $listItem->last_price_total }}</span>
					</td>
					<td class="@if(!GROCY_FEATURE_FLAG_STOCK_PRICE_TRACKING) hidden @endif">
						{{ $listItem->default_shopping_location_name }}
					</td>
					<td>
						@if($listItem->product_barcodes != null)
						@foreach(explode(',', $listItem->product_barcodes) as $barcode)
						@if(!empty($barcode))
						<img class="barcode max-w-full pr-2"
							data-barcode="{{ $barcode }}">
						@endif
						@endforeach
						@endif
					</td>

					@include('components.userfields_tbody', array(
					'userfields' => $userfields,
					'userfieldValues' => FindAllObjectsInArrayByPropertyValue($userfieldValues, 'object_id', $listItem->id)
					))
					@include('components.userfields_tbody', array(
					'userfields' => $productUserfields,
					'userfieldValues' => FindAllObjectsInArrayByPropertyValue($productUserfieldValues, 'object_id', $listItem->product_id)
					))

				</tr>
				@endforeach
			</tbody>
		</table>
	</div>

	@if(boolval($userSettings['shopping_list_show_calendar']))
	<div class="w-full md:w-1/3 md:mt-2 print:hidden md:pl-4">
		@include('components.calendarcard')
	</div>
	@endif

	<div class="@if(boolval($userSettings['shopping_list_show_calendar'])) w-full md:w-2/3 @else w-full @endif print:hidden pt-2">
		<div class="form-group">
			<label class="text-lg font-bold text-gray-900 dark:text-gray-100"
				for="notes">{{ $__t('Notes') }}</label>
			<a id="save-description-button"
				class="btn btn-success btn-sm ml-1 mb-2"
				href="#">{{ $__t('Save') }}</a>
			<a id="clear-description-button"
				class="btn btn-danger btn-sm ml-1 mb-2"
				href="#">{{ $__t('Clear') }}</a>
			<textarea class="input wysiwyg-editor"
				id="description"
				name="description">{{ FindObjectInArrayByPropertyValue($shoppingLists, 'id', $selectedShoppingListId)->description }}</textarea>
		</div>
	</div>
</div>

<div class="fixed inset-0 z-50 overflow-y-auto hidden"
	id="shopping-list-stock-add-workflow-modal"
	tabindex="-1">
	<div class="flex min-h-full items-center justify-center p-4">
		<div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-lg w-full text-center">
			<div class="p-6">
				<iframe id="shopping-list-stock-add-workflow-purchase-form-frame"
					class="w-full aspect-video">
				</iframe>
			</div>
			<div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 flex justify-end gap-2 hidden">
				<span id="shopping-list-stock-add-workflow-purchase-item-count"
					class="hidden mr-auto"></span>
				<button id="shopping-list-stock-add-workflow-skip-button"
					type="button"
					class="btn btn-primary">{{ $__t('Skip') }}</button>
			</div>
		</div>
	</div>
</div>

<div class="hidden print:block">
	<div id="print-header">
		<h1 class="text-center text-2xl font-bold">
			<img src="{{ $U('/img/logo.svg?v=', true) }}{{ $version }}"
				width="114"
				height="30"
				class="print:flex mx-auto">
			{{ $__t("Shopping list") }}
		</h1>
		@if (FindObjectInArrayByPropertyValue($shoppingLists, 'id', $selectedShoppingListId)->name != $__t("Shopping list"))
		<h3 class="text-center text-xl">
			{{ FindObjectInArrayByPropertyValue($shoppingLists, 'id', $selectedShoppingListId)->name }}
		</h3>
		@endif
		<h6 class="text-center mb-4 text-sm text-gray-500">
			{{ $__t('Time of printing') }}:
			<span class="inline print-timestamp"></span>
		</h6>
	</div>
	<div class="w-3/4 mx-auto print-layout-container print-layout-type-table hidden">
		<div>
			<table id="shopping-list-print-shadow-table"
				class="table table-sm table-striped nowrap w-full">
				<thead>
					<tr>
						<th>{{ $__t('Product') }} / <em>{{ $__t('Note') }}</em></th>
						<th>{{ $__t('Amount') }}</th>
						<th>{{ $__t('Product group') }}</th>

						@include('components.userfields_thead', array(
						'userfields' => $userfields
						))
						@include('components.userfields_thead', array(
						'userfields' => $productUserfields
						))
						@include('components.userfields_thead', array(
						'userfields' => $productGroupUserfields
						))
					</tr>
				</thead>
				<tbody>
					@foreach($listItems as $listItem)
					<tr>
						<td>
							@if(!empty($listItem->product_id)) {{ $listItem->product_name }}<br>@endif<em>{!! nl2br($listItem->note ?? '') !!}</em>
						</td>
						<td>
							<span class="locale-number locale-number-quantity-amount">{{ $listItem->amount }}</span> @if(!empty($listItem->product_id)){{ $__n($listItem->amount, $listItem->qu_name, $listItem->qu_name_plural, true) }}@endif
						</td>
						<td>
							@if(!empty($listItem->product_group_name)) {{ $listItem->product_group_name }} @else <span class="italic font-light">{{ $__t('Ungrouped') }}</span> @endif
						</td>

						@include('components.userfields_tbody', array(
						'userfields' => $userfields,
						'userfieldValues' => FindAllObjectsInArrayByPropertyValue($userfieldValues, 'object_id', $listItem->id)
						))
						@include('components.userfields_tbody', array(
						'userfields' => $productUserfields,
						'userfieldValues' => FindAllObjectsInArrayByPropertyValue($productUserfieldValues, 'object_id', $listItem->product_id)
						))
						@include('components.userfields_tbody', array(
						'userfields' => $productGroupUserfields,
						'userfieldValues' => FindAllObjectsInArrayByPropertyValue($productGroupUserfieldValues, 'object_id', $listItem->product_group_id)
						))

					</tr>
					@endforeach
				</tbody>
			</table>
		</div>
	</div>
	<div class="w-3/4 mx-auto print-layout-container print-layout-type-list hidden">
		@foreach($listItems as $listItem)
		<div class="py-0">
			<span class="locale-number locale-number-quantity-amount">{{ $listItem->amount }}</span> @if(!empty($listItem->product_id)){{ $__n($listItem->amount, $listItem->qu_name, $listItem->qu_name_plural, true) }}@endif
			@if(!empty($listItem->product_id)) {{ $listItem->product_name }}<br>@endif<em>{!! nl2br($listItem->note ?? '') !!}</em>
		</div><br>
		@endforeach
	</div>
	<div class="w-3/4 mx-auto pt-3">
		<div>
			<h5 class="font-bold">{{ $__t('Notes') }}</h5>
			<p id="description-for-print"></p>
		</div>
	</div>
</div>

@include('components.productcard', [
'asModal' => true
])
@stop
