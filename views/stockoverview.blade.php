@php require_frontend_packages(['datatables', 'animatecss']); @endphp

@extends('layout.default')

@section('title', $__t('Stock overview'))

@push('pageScripts')
<script src="{{ $U('/viewjs/purchase.js?v=', true) }}{{ $version }}"></script>
@endpush

@section('content')
<div class="flex flex-wrap">
	<div class="w-full">
		<div class="flex flex-wrap items-center gap-2">
			<h2 class="text-xl font-bold text-gray-900 dark:text-gray-100 mr-2">
				@yield('title')
			</h2>
			<h2 class="mb-0 mr-auto w-full md:w-auto order-3 md:order-1">
				<span id="info-current-stock"
					class="text-gray-500 dark:text-gray-400 text-sm"></span>
			</h2>
			<button class="btn btn-secondary md:hidden mt-2 float-right order-1 md:order-3"
				type="button"
				data-toggle="collapse"
				data-target="#related-links">
				<i class="fa-solid fa-ellipsis-v"></i>
			</button>
			<div class="related-links collapse md:flex order-2 w-full md:w-auto"
				id="related-links">
				<a class="btn btn-secondary m-1 md:mt-0 md:mb-0 float-right"
					href="{{ $U('/stockjournal') }}">
					{{ $__t('Journal') }}
				</a>
				<a class="btn btn-secondary m-1 md:mt-0 md:mb-0 float-right"
					href="{{ $U('/stockentries') }}">
					{{ $__t('Stock entries') }}
				</a>
				@if(GROCY_FEATURE_FLAG_STOCK_LOCATION_TRACKING || GROCY_FEATURE_FLAG_STOCK_PRICE_TRACKING)
				<div class="dropdown inline-block"
					x-data="{ open: false }">
					<a class="btn btn-secondary m-1 md:mt-0 md:mb-0 float-right inline-flex items-center gap-1"
						href="#"
						@click.prevent="open = !open">
						{{ $__t('Reports') }}
						<i class="fa-solid fa-chevron-down text-xs"></i>
					</a>
					<div class="dropdown-menu absolute bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow-lg py-1 mt-1 min-w-40 z-50"
						x-show="open"
						@click.outside="open = false"
						x-cloak>
						@if(GROCY_FEATURE_FLAG_STOCK_LOCATION_TRACKING)
						<a class="dropdown-item"
							href="{{ $U('/locationcontentsheet') }}">{{ $__t('Location Content Sheet') }}</a>
						@endif
						@if(GROCY_FEATURE_FLAG_STOCK_PRICE_TRACKING)
						<a class="dropdown-item"
							href="{{ $U('/stockreports/spendings') }}">{{ $__t('Spendings') }}</a>
						@endif
					</div>
				</div>
				@endif
			</div>
		</div>
		<div class="border-t border-b border-gray-200 dark:border-gray-700 my-2 py-1">
			@if (GROCY_FEATURE_FLAG_STOCK_BEST_BEFORE_DATE_TRACKING)
			<div id="info-expired-products"
				data-status-filter="expired"
				class="error-message status-filter-message responsive-button mr-2"></div>
			<div id="info-overdue-products"
				data-status-filter="overdue"
				class="secondary-message status-filter-message responsive-button mr-2"></div>
			<div id="info-duesoon-products"
				data-next-x-days="{{ $nextXDays }}"
				data-status-filter="duesoon"
				class="warning-message status-filter-message responsive-button mr-2"></div>
			@endif
			<div id="info-missing-products"
				data-status-filter="belowminstockamount"
				class="normal-message status-filter-message responsive-button"></div>
			<div class="float-right mt-1 @if($embedded) pr-5 @endif">
				<a class="btn btn-sm text-blue-600 dark:text-blue-400 border border-blue-600 dark:border-blue-400 md:hidden"
					data-toggle="collapse"
					href="#table-filter-row"
					role="button">
					<i class="fa-solid fa-filter"></i>
				</a>
				<button id="clear-filter-button"
					class="btn btn-sm text-blue-600 dark:text-blue-400 border border-blue-600 dark:border-blue-400"
					data-toggle="tooltip"
					title="{{ $__t('Clear filter') }}">
					<i class="fa-solid fa-filter-circle-xmark"></i>
				</button>
			</div>
		</div>
	</div>
</div>
<div class="flex flex-wrap collapse md:flex"
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
	@if(GROCY_FEATURE_FLAG_STOCK_LOCATION_TRACKING)
	<div class="w-full md:w-1/2 xl:w-1/4 px-2 mb-2">
		<div class="input-group flex">
			<span class="input-group-text rounded-l-md border-r-0"><i class="fa-solid fa-filter"></i>&nbsp;{{ $__t('Location') }}</span>
			<select class="select flex-1 rounded-l-none"
				id="location-filter">
				<option value="all">{{ $__t('All') }}</option>
				@foreach($locations as $location)
				<option value="{{ $location->name }}">{{ $location->name }}</option>
				@endforeach
			</select>
		</div>
	</div>
	@endif
	<div class="w-full md:w-1/2 xl:w-1/4 px-2 mb-2">
		<div class="input-group flex">
			<span class="input-group-text rounded-l-md border-r-0"><i class="fa-solid fa-filter"></i>&nbsp;{{ $__t('Product group') }}</span>
			<select class="select flex-1 rounded-l-none"
				id="product-group-filter">
				<option value="all">{{ $__t('All') }}</option>
				@foreach($productGroups as $productGroup)
				<option value="{{ $productGroup->name }}">{{ $productGroup->name }}</option>
				@endforeach
			</select>
		</div>
	</div>
	<div class="w-full md:w-1/2 xl:w-1/4 px-2 mb-2">
		<div class="input-group flex">
			<span class="input-group-text rounded-l-md border-r-0"><i class="fa-solid fa-filter"></i>&nbsp;{{ $__t('Status') }}</span>
			<select class="select flex-1 rounded-l-none"
				id="status-filter">
				<option class="bg-white dark:bg-gray-800"
					value="all">{{ $__t('All') }}</option>
				@if (GROCY_FEATURE_FLAG_STOCK_BEST_BEFORE_DATE_TRACKING)
				<option value="duesoon">{{ $__t('Due soon') }}</option>
				<option value="overdue">{{ $__t('Overdue') }}</option>
				<option value="expired">{{ $__t('Expired') }}</option>
				@endif
				<option value="belowminstockamount">{{ $__t('Below min. stock amount') }}</option>
				<option value="instockX">{{ $__t('In stock products') }}</option>
			</select>
		</div>
	</div>
</div>

<div class="flex flex-wrap">
	<div class="w-full">
		<table id="stock-overview-table"
			class="table table-sm table-striped nowrap w-full">
			<thead>
				<tr>
					<th class="border-r border-gray-200 dark:border-gray-700"><a class="text-gray-500 dark:text-gray-400 change-table-columns-visibility-button"
							data-toggle="tooltip"
							title="{{ $__t('Table options') }}"
							data-table-selector="#stock-overview-table"
							href="#"><i class="fa-solid fa-eye"></i></a>
					</th>
					<th>{{ $__t('Product') }}</th>
					<th class="allow-grouping">{{ $__t('Product group') }}</th>
					<th>{{ $__t('Amount') }}</th>
					<th class="@if(!GROCY_FEATURE_FLAG_STOCK_PRICE_TRACKING) hidden @endif">{{ $__t('Value') }}</th>
					<th class="@if(!GROCY_FEATURE_FLAG_STOCK_BEST_BEFORE_DATE_TRACKING) hidden @endif allow-grouping">{{ $__t('Next due date') }}</th>
					<th class="hidden">Hidden location</th>
					<th class="hidden">Hidden status</th>
					<th class="hidden">Hidden product group</th>
					<th>{{ $__t('Calories') }} ({{ $__t('Per stock quantity unit') }})</th>
					<th>{{ $__t('Calories') }}</th>
					<th class="allow-grouping">{{ $__t('Last purchased') }}</th>
					<th class="@if(!GROCY_FEATURE_FLAG_STOCK_PRICE_TRACKING) hidden @endif">{{ $__t('Last price') }}</th>
					<th class="allow-grouping">{{ $__t('Min. stock amount') }}</th>
					<th>{{ $__t('Product description') }}</th>
					<th class="allow-grouping">{{ $__t('Parent product') }}</th>
					<th class="allow-grouping">{{ $__t('Default location') }}</th>
					<th>{{ $__t('Product picture') }}</th>
					<th class="@if(!GROCY_FEATURE_FLAG_STOCK_PRICE_TRACKING) hidden @endif">{{ $__t('Average price') }}</th>
					<th class="@if(!GROCY_FEATURE_FLAG_STOCK_PRICE_TRACKING) hidden @endif allow-grouping">{{ $__t('Default store') }}</th>

					@include('components.userfields_thead', array(
					'userfields' => $userfields
					))

				</tr>
			</thead>
			<tbody class="hidden">
				@foreach($currentStock as $currentStockEntry)
				<tr id="product-{{ $currentStockEntry->product_id }}-row"
					class="@if(GROCY_FEATURE_FLAG_STOCK_BEST_BEFORE_DATE_TRACKING && $currentStockEntry->best_before_date < date('Y-m-d 23:59:59', strtotime('-1 days')) && $currentStockEntry->amount > 0) @if($currentStockEntry->due_type == 1) bg-gray-200 dark:bg-gray-700 @else bg-red-100 dark:bg-red-900/30 @endif @elseif(GROCY_FEATURE_FLAG_STOCK_BEST_BEFORE_DATE_TRACKING && $currentStockEntry->best_before_date < date('Y-m-d 23:59:59', strtotime('+' . $nextXDays . ' days')) && $currentStockEntry->amount > 0) bg-yellow-100 dark:bg-yellow-900/30 @elseif ($currentStockEntry->product_missing) bg-blue-100 dark:bg-blue-900/30 @endif">
					<td class="fit-content border-r border-gray-200 dark:border-gray-700">
						<a class="permission-STOCK_CONSUME btn btn-success btn-sm product-consume-button @if($currentStockEntry->amount_aggregated < $currentStockEntry->quick_consume_amount || $currentStockEntry->enable_tare_weight_handling == 1) opacity-50 pointer-events-none @endif"
							href="#"
							data-toggle="tooltip"
							data-placement="left"
							title="{{ $__t('Consume %1$s of %2$s', $currentStockEntry->quick_consume_amount_qu_consume . ' ' . $currentStockEntry->qu_consume_name, $currentStockEntry->product_name) }}"
							data-product-id="{{ $currentStockEntry->product_id }}"
							data-product-name="{{ $currentStockEntry->product_name }}"
							data-product-qu-name="{{ $currentStockEntry->qu_stock_name }}"
							data-consume-amount="{{ $currentStockEntry->quick_consume_amount }}">
							<i class="fa-solid fa-utensils"></i> <span class="locale-number locale-number-quantity-amount">{{ $currentStockEntry->quick_consume_amount_qu_consume }}</span>
						</a>
						<a id="product-{{ $currentStockEntry->product_id }}-consume-all-button"
							class="permission-STOCK_CONSUME btn btn-danger btn-sm product-consume-button @if($currentStockEntry->amount_aggregated == 0) opacity-50 pointer-events-none @endif"
							href="#"
							data-toggle="tooltip"
							data-placement="right"
							title="{{ $__t('Consume all %s which are currently in stock', $currentStockEntry->product_name) }}"
							data-product-id="{{ $currentStockEntry->product_id }}"
							data-product-name="{{ $currentStockEntry->product_name }}"
							data-product-qu-name="{{ $currentStockEntry->qu_stock_name }}"
							data-consume-amount="@if($currentStockEntry->enable_tare_weight_handling == 1){{$currentStockEntry->tare_weight}}@else{{$currentStockEntry->amount}}@endif"
							data-original-total-stock-amount="{{$currentStockEntry->amount}}">
							<i class="fa-solid fa-utensils"></i> {{ $__t('All') }}
						</a>
						@if(GROCY_FEATURE_FLAG_STOCK_PRODUCT_OPENED_TRACKING)
						<a class="btn btn-success btn-sm product-open-button @if($currentStockEntry->amount_aggregated < $currentStockEntry->quick_open_amount || $currentStockEntry->amount_aggregated == $currentStockEntry->amount_opened_aggregated || $currentStockEntry->enable_tare_weight_handling == 1 || $currentStockEntry->disable_open == 1) opacity-50 pointer-events-none @endif"
							href="#"
							data-toggle="tooltip"
							data-placement="left"
							title="{{ $__t('Mark %1$s of %2$s as open', $currentStockEntry->quick_open_amount_qu_consume . ' ' . $currentStockEntry->qu_consume_name, $currentStockEntry->product_name) }}"
							data-product-id="{{ $currentStockEntry->product_id }}"
							data-product-name="{{ $currentStockEntry->product_name }}"
							data-product-qu-name="{{ $currentStockEntry->qu_stock_name }}"
							data-open-amount="{{ $currentStockEntry->quick_open_amount }}">
							<i class="fa-solid fa-box-open"></i> <span class="locale-number locale-number-quantity-amount">{{ $currentStockEntry->quick_open_amount_qu_consume }}</span>
						</a>
						@endif
						<div class="dropdown inline-block"
							x-data="{ open: false }">
							<button class="btn btn-sm bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400"
								type="button"
								@click="open = !open">
								<i class="fa-solid fa-ellipsis-v"></i>
							</button>
							<div class="table-inline-menu dropdown-menu absolute right-0 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow-lg py-1 mt-1 min-w-48 z-50"
								x-show="open"
								@click.outside="open = false"
								x-cloak>
								@if(GROCY_FEATURE_FLAG_SHOPPINGLIST)
								<a class="dropdown-item show-as-dialog-link permission-SHOPPINGLIST_ITEMS_ADD"
									type="button"
									href="{{ $U('/shoppinglistitem/new?embedded&updateexistingproduct&list=1&product=' . $currentStockEntry->product_id ) }}">
									<span class="dropdown-item-icon"><i class="fa-solid fa-shopping-cart"></i></span> <span class="dropdown-item-text">{{ $__t('Add to shopping list') }}</span>
								</a>
								<div class="border-t border-gray-200 dark:border-gray-700 my-1"></div>
								@endif
								<a class="dropdown-item show-as-dialog-link permission-STOCK_PURCHASE"
									type="button"
									href="{{ $U('/purchase?embedded&product=' . $currentStockEntry->product_id ) }}">
									<span class="dropdown-item-icon"><i class="fa-solid fa-cart-plus"></i></span> <span class="dropdown-item-text">{{ $__t('Purchase') }}</span>
								</a>
								<a class="dropdown-item show-as-dialog-link permission-STOCK_CONSUME @if($currentStockEntry->amount_aggregated <= 0) opacity-50 pointer-events-none @endif"
									type="button"
									href="{{ $U('/consume?embedded&product=' . $currentStockEntry->product_id ) }}">
									<span class="dropdown-item-icon"><i class="fa-solid fa-utensils"></i></span> <span class="dropdown-item-text">{{ $__t('Consume') }}</span>
								</a>
								@if(GROCY_FEATURE_FLAG_STOCK_LOCATION_TRACKING)
								<a class="dropdown-item show-as-dialog-link permission-STOCK_TRANSFER @if($currentStockEntry->amount <= 0) opacity-50 pointer-events-none @endif"
									type="button"
									href="{{ $U('/transfer?embedded&product=' . $currentStockEntry->product_id) }}">
									<span class="dropdown-item-icon"><i class="fa-solid fa-exchange-alt"></i></span> <span class="dropdown-item-text">{{ $__t('Transfer') }}</span>
								</a>
								@endif
								<a class="dropdown-item show-as-dialog-link permission-STOCK_INVENTORY"
									type="button"
									href="{{ $U('/inventory?embedded&product=' . $currentStockEntry->product_id ) }}">
									<span class="dropdown-item-icon"><i class="fa-solid fa-list"></i></span> <span class="dropdown-item-text">{{ $__t('Inventory') }}</span>
								</a>
								@if(GROCY_FEATURE_FLAG_RECIPES)
								<div class="border-t border-gray-200 dark:border-gray-700 my-1"></div>
								<a class="dropdown-item"
									type="button"
									href="{{ $U('/recipes?search=') }}{{ $currentStockEntry->product_name }}">
									<span class="dropdown-item-text">{{ $__t('Search for recipes containing this product') }}</span>
								</a>
								@endif
								<div class="border-t border-gray-200 dark:border-gray-700 my-1"></div>
								<a class="dropdown-item productcard-trigger"
									data-product-id="{{ $currentStockEntry->product_id }}"
									type="button"
									href="#">
									<span class="dropdown-item-text">{{ $__t('Product overview') }}</span>
								</a>
								<a class="dropdown-item show-as-dialog-link"
									type="button"
									href="{{ $U('/stockentries?embedded&product=') }}{{ $currentStockEntry->product_id }}"
									data-dialog-type="table"
									data-product-id="{{ $currentStockEntry->product_id }}">
									<span class="dropdown-item-text">{{ $__t('Stock entries') }}</span>
								</a>
								<a class="dropdown-item show-as-dialog-link"
									type="button"
									href="{{ $U('/stockjournal?embedded&product=') }}{{ $currentStockEntry->product_id }}"
									data-dialog-type="table">
									<span class="dropdown-item-text">{{ $__t('Stock journal') }}</span>
								</a>
								<a class="dropdown-item show-as-dialog-link"
									type="button"
									href="{{ $U('/stockjournal/summary?embedded&product_id=') }}{{ $currentStockEntry->product_id }}"
									data-dialog-type="table">
									<span class="dropdown-item-text">{{ $__t('Stock journal summary') }}</span>
								</a>
								<a class="dropdown-item permission-MASTER_DATA_EDIT link-return"
									type="button"
									data-href="{{ $U('/product/') }}{{ $currentStockEntry->product_id }}">
									<span class="dropdown-item-text">{{ $__t('Edit product') }}</span>
								</a>
								<div class="border-t border-gray-200 dark:border-gray-700 my-1"></div>
								<a class="dropdown-item"
									type="button"
									href="{{ $U('/product/' . $currentStockEntry->product_id . '/grocycode?download=true') }}">
									{!! str_replace('Grocycode', '<span class="tracking-tight">Grocycode</span>', $__t('Download %s Grocycode', $__t('Product'))) !!}
								</a>
								@if(GROCY_FEATURE_FLAG_LABEL_PRINTER)
								<a class="dropdown-item product-grocycode-label-print"
									data-product-id="{{ $currentStockEntry->product_id }}"
									type="button"
									href="#">
									{!! str_replace('Grocycode', '<span class="tracking-tight">Grocycode</span>', $__t('Print %s Grocycode on label printer', $__t('Product'))) !!}
								</a>
								@endif
							</div>
						</div>
					</td>
					<td class="productcard-trigger cursor-pointer"
						data-product-id="{{ $currentStockEntry->product_id }}">
						{{ $currentStockEntry->product_name }}
						<span class="hidden">{{ $currentStockEntry->product_barcodes }}</span>
					</td>
					<td>
						@if($currentStockEntry->product_group_name !== null){{ $currentStockEntry->product_group_name }}@endif
					</td>
					<td>
						<span class="custom-sort hidden">@if($currentStockEntry->product_no_own_stock == 1){{ $currentStockEntry->amount_aggregated }}@else{{ $currentStockEntry->amount }}@endif</span>
						<span class="@if($currentStockEntry->product_no_own_stock == 1) hidden @endif">
							<span id="product-{{ $currentStockEntry->product_id }}-amount"
								class="locale-number locale-number-quantity-amount">{{ $currentStockEntry->amount }}</span> <span id="product-{{ $currentStockEntry->product_id }}-qu-name">{{ $__n($currentStockEntry->amount, $currentStockEntry->qu_stock_name, $currentStockEntry->qu_stock_name_plural) }}</span>
							<span id="product-{{ $currentStockEntry->product_id }}-opened-amount"
								class="text-sm italic">@if($currentStockEntry->amount_opened > 0){{ $__t('%s opened', $currentStockEntry->amount_opened) }}@endif</span>
						</span>
						@if($currentStockEntry->is_aggregated_amount == 1)
						<span class="@if($currentStockEntry->product_no_own_stock == 0) pl-1 @endif text-gray-500 dark:text-gray-400">
							<i class="fa-solid fa-custom-sigma-sign"></i> <span id="product-{{ $currentStockEntry->product_id }}-amount-aggregated"
								class="locale-number locale-number-quantity-amount">{{ $currentStockEntry->amount_aggregated }}</span> {{ $__n($currentStockEntry->amount_aggregated, $currentStockEntry->qu_stock_name, $currentStockEntry->qu_stock_name_plural, true) }}
							@if($currentStockEntry->amount_opened_aggregated > 0)
							<span id="product-{{ $currentStockEntry->product_id }}-opened-amount-aggregated"
								class="text-sm italic">
								{!! $__t('%s opened', '<span class="locale-number locale-number-quantity-amount">' . $currentStockEntry->amount_opened_aggregated . '</span>') !!}
							</span>
							@endif
						</span>
						@endif
						@if(boolval($userSettings['show_icon_on_stock_overview_page_when_product_is_on_shopping_list']))
						@if($currentStockEntry->on_shopping_list)
						<span class="text-gray-500 dark:text-gray-400 cursor-default"
							data-toggle="tooltip"
							title="{{ $__t('This product is currently on a shopping list') }}">
							<i class="fa-solid fa-shopping-cart"></i>
						</span>
						@endif
						@endif
					</td>
					<td>
						<span class="custom-sort hidden">{{$currentStockEntry->value}}</span>
						<span id="product-{{ $currentStockEntry->product_id }}-value"
							class="locale-number locale-number-currency">{{ $currentStockEntry->value }}</span>
					</td>
					<td class="@if(!GROCY_FEATURE_FLAG_STOCK_BEST_BEFORE_DATE_TRACKING) hidden @endif">
						<span id="product-{{ $currentStockEntry->product_id }}-next-due-date">{{ $currentStockEntry->best_before_date }}</span>
						<time id="product-{{ $currentStockEntry->product_id }}-next-due-date-timeago"
							class="timeago timeago-contextual"
							@if(!empty($currentStockEntry->best_before_date)) datetime="{{ $currentStockEntry->best_before_date }} 23:59:59" @endif></time>
					</td>
					<td class="hidden">
						@foreach(FindAllObjectsInArrayByPropertyValue($currentStockLocations, 'product_id', $currentStockEntry->product_id) as $locationsForProduct)
						xx{{ FindObjectInArrayByPropertyValue($locations, 'id', $locationsForProduct->location_id)->name }}xx
						@endforeach
					</td>
					<td class="hidden">
						@if($currentStockEntry->best_before_date < date('Y-m-d
							23:59:59',
							strtotime('-'
							. '1'
							. ' days'
							))
							&&
							$currentStockEntry->amount > 0) @if($currentStockEntry->due_type == 1) overdue @else expired @endif @elseif($currentStockEntry->best_before_date < date('Y-m-d
								23:59:59',
								strtotime('+'
								.
								$nextXDays
								. ' days'
								))
								&&
								$currentStockEntry->amount > 0) duesoon @endif
								@if($currentStockEntry->amount_aggregated > 0) instockX @endif
								@if ($currentStockEntry->product_missing) belowminstockamount @endif
					</td>
					<td class="hidden">
						xx{{ $currentStockEntry->product_group_name }}xx
					</td>
					<td>
						<span class="locale-number locale-number-quantity-amount">{{ $currentStockEntry->product_calories }}</span>
					</td>
					<td>
						<span class="locale-number locale-number-quantity-amount">{{ $currentStockEntry->calories }}</span>
					</td>
					<td>
						{{ $currentStockEntry->last_purchased }}
						<time class="timeago timeago-contextual"
							datetime="{{ $currentStockEntry->last_purchased }}"></time>
					</td>
					<td class="@if(!GROCY_FEATURE_FLAG_STOCK_PRICE_TRACKING) hidden @endif">
						<span class="custom-sort hidden">{{$currentStockEntry->last_price}}</span>
						@if(!empty($currentStockEntry->last_price))
						<span data-toggle="tooltip"
							data-trigger="hover click"
							data-html="true"
							title="{!! $__t('%1$s per %2$s', '<span class=\'locale-number locale-number-currency\'>' . $currentStockEntry->last_price . '</span>', $currentStockEntry->qu_stock_name) !!}">
							{!! $__t('%1$s per %2$s', '<span class="locale-number locale-number-currency">' . $currentStockEntry->last_price * $currentStockEntry->product_qu_factor_price_to_stock . '</span>', $currentStockEntry->qu_price_name) !!}
						</span>
						@endif
					</td>
					<td>
						<span class="locale-number locale-number-quantity-amount">{{ $currentStockEntry->min_stock_amount }}</span>
					</td>
					<td>
						{!! $currentStockEntry->product_description !!}
					</td>
					<td class="productcard-trigger cursor-pointer"
						data-product-id="{{ $currentStockEntry->parent_product_id }}">
						{{ $currentStockEntry->parent_product_name }}
					</td>
					<td>
						{{ $currentStockEntry->product_default_location_name }}
					</td>
					<td>
						@if(!empty($currentStockEntry->product_picture_file_name))
						<img src="{{ $U('/api/files/productpictures/' . base64_encode($currentStockEntry->product_picture_file_name) . '?force_serve_as=picture&best_fit_width=64&best_fit_height=64') }}"
							loading="lazy">
						@endif
					</td>
					<td class="@if(!GROCY_FEATURE_FLAG_STOCK_PRICE_TRACKING) hidden @endif">
						<span class="custom-sort hidden">{{$currentStockEntry->average_price}}</span>
						@if(!empty($currentStockEntry->average_price))
						<span data-toggle="tooltip"
							data-trigger="hover click"
							data-html="true"
							title="{!! $__t('%1$s per %2$s', '<span class=\'locale-number locale-number-currency\'>' . $currentStockEntry->average_price . '</span>', $currentStockEntry->qu_stock_name) !!}">
							{!! $__t('%1$s per %2$s', '<span class="locale-number locale-number-currency">' . $currentStockEntry->average_price * $currentStockEntry->product_qu_factor_price_to_stock . '</span>', $currentStockEntry->qu_price_name) !!}
						</span>
						@endif
					</td>
					<td>
						@if($currentStockEntry->default_store_name !== null){{ $currentStockEntry->default_store_name }}@endif
					</td>

					@include('components.userfields_tbody', array(
					'userfields' => $userfields,
					'userfieldValues' => FindAllObjectsInArrayByPropertyValue($userfieldValues, 'object_id', $currentStockEntry->product_id)
					))

				</tr>
				@endforeach
			</tbody>
		</table>
	</div>
</div>

@include('components.productcard', [
'asModal' => true
])
@stop
