@php require_frontend_packages(['chartjs']); @endphp

@once
@push('componentScripts')
<script src="{{ $U('/viewjs/components/productcard.js', true) }}?v={{ $version }}"></script>
@endpush
@endonce

@php if(!isset($asModal)) { $asModal = false; } @endphp

@if($asModal)
<div x-data="{ show: false }"
	id="productcard-modal"
	tabindex="-1"
	class="fixed inset-0 z-50 overflow-y-auto hidden"
	aria-labelledby="modal-title"
	role="dialog"
	aria-modal="true">
	<div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
		<div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
			aria-hidden="true"></div>
		<span class="hidden sm:inline-block sm:align-middle sm:h-screen"
			aria-hidden="true">&#8203;</span>
		<div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-center overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
			<div class="p-4">
				@endif

				<div class="card productcard">
					<div class="card-header flex items-center justify-between">
						<span class="text-left">{{ $__t('Product overview') }}</span>
						<div class="flex gap-1">
							<a id="productcard-product-stock-button"
								class="btn-secondary text-xs px-2 py-1 disabled show-as-dialog-link"
								href="#"
								data-dialog-type="table">
								{{ $__t('Stock entries') }}
							</a>
							<a id="productcard-product-journal-button"
								class="btn-secondary text-xs px-2 py-1 disabled show-as-dialog-link"
								href="#"
								data-dialog-type="table">
								{{ $__t('Stock journal') }}
							</a>
							@if(GROCY_FEATURE_FLAG_SHOPPINGLIST)
							<a id="productcard-product-shoppinglist-button"
								class="btn-secondary text-xs px-2 py-1 disabled show-as-dialog-link"
								href="#"
								data-tooltip
								title="{{ $__t('Add to shopping list') }}">
								<i class="fa-solid fa-shopping-cart"></i>
							</a>
							@endif
							<a id="productcard-product-edit-button"
								class="btn-secondary text-xs px-2 py-1 disabled"
								href="#"
								data-tooltip
								title="{{ $__t('Edit product') }}">
								<i class="fa-solid fa-edit"></i>
							</a>
						</div>
					</div>
					<div class="card-body">
						<h3 class="text-2xl font-bold mb-2"><span id="productcard-product-name"></span></h3>

						<div id="productcard-product-description-wrapper"
							class="expandable-text mb-2 hidden"
							x-data="{ expanded: false }">
							<p id="productcard-product-description"
								class="text-gray-500 dark:text-gray-400 mb-0"
								:class="{ 'line-clamp-3': !expanded }"></p>
							<a class="text-primary-600 hover:text-primary-700 text-sm cursor-pointer"
								@click="expanded = !expanded"
								x-text="expanded ? '{{ $__t('Show less') }}' : '{{ $__t('Show more') }}'"></a>
						</div>

						<div class="text-left space-y-1">
							<div>
								<strong>{{ $__t('Stock amount') }}:</strong>
								<span id="productcard-product-stock-amount-wrapper">
									<span id="productcard-product-stock-amount"
										class="locale-number locale-number-quantity-amount"></span> <span id="productcard-product-stock-qu-name"></span>
								</span>
								<span id="productcard-product-stock-opened-amount"
									class="text-sm italic"></span>
								<span id="productcard-aggregated-amounts"
									class="pl-2 text-gray-600 dark:text-gray-400 hidden"><i class="fa-solid fa-custom-sigma-sign"></i> <span id="productcard-product-stock-amount-aggregated"
										class="locale-number locale-number-quantity-amount"></span> <span id="productcard-product-stock-qu-name-aggregated"></span> <span id="productcard-product-stock-opened-amount-aggregated"
										class="text-sm italic"></span></span>
							</div>

							@if(GROCY_FEATURE_FLAG_STOCK_PRICE_TRACKING)
							<div>
								<strong>{{ $__t('Stock value') }}:</strong> <span id="productcard-product-stock-value"
									class="locale-number locale-number-currency"></span>
							</div>
							@endif

							@if(GROCY_FEATURE_FLAG_STOCK_LOCATION_TRACKING)
							<div>
								<strong>{{ $__t('Default location') }}:</strong> <span id="productcard-product-location"></span>
							</div>
							@endif
							<div>
								<strong>{{ $__t('Last purchased') }}:</strong> <span id="productcard-product-last-purchased"></span> <time id="productcard-product-last-purchased-timeago"
									class="timeago timeago-contextual"></time>
							</div>
							<div>
								<strong>{{ $__t('Last used') }}:</strong> <span id="productcard-product-last-used"></span> <time id="productcard-product-last-used-timeago"
									class="timeago timeago-contextual"></time>
							</div>

							@if(GROCY_FEATURE_FLAG_STOCK_PRICE_TRACKING)
							<div>
								<strong>{{ $__t('Last price') }}:</strong> <span id="productcard-product-last-price"
									data-tooltip
									data-trigger="hover click"></span>
							</div>
							@endif

							@if(GROCY_FEATURE_FLAG_STOCK_PRICE_TRACKING)
							<div>
								<strong>{{ $__t('Average price') }}:</strong> <span id="productcard-product-average-price"
									data-tooltip
									data-trigger="hover click"></span>
							</div>
							@endif

							@if(GROCY_FEATURE_FLAG_STOCK_BEST_BEFORE_DATE_TRACKING)
							<div>
								<strong>{{ $__t('Average shelf life') }}:</strong> <span id="productcard-product-average-shelf-life"></span>
							</div>
							@endif
							<div>
								<strong>{{ $__t('Spoil rate') }}:</strong> <span id="productcard-product-spoil-rate"></span>
							</div>
						</div>

						<p class="w-3/4 mt-3 mx-auto">
							<img id="productcard-product-picture"
								class="w-full rounded border border-gray-200 dark:border-gray-700 hidden"
								src=""
								loading="lazy">
						</p>

						@if(GROCY_FEATURE_FLAG_STOCK_PRICE_TRACKING)
						<h5 class="mt-3 text-lg font-semibold">{{ $__t('Price history') }}</h5>
						<canvas id="productcard-product-price-history-chart"
							class="w-full hidden"></canvas>
						<span id="productcard-no-price-data-hint"
							class="italic hidden">{{ $__t('No price history available') }}</span>
						@endif
					</div>
				</div>

				@if($asModal)
			</div>
			<div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
				<button type="button"
					class="btn-secondary w-full sm:w-auto"
					data-dismiss="modal">{{ $__t('Close') }}</button>
			</div>
		</div>
	</div>
</div>
@endif
