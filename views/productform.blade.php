@php require_frontend_packages(['datatables', 'summernote']); @endphp

@extends('layout.default')

@if($mode == 'edit')
@section('title', $__t('Edit product'))
@else
@section('title', $__t('Create product'))
@endif

@section('content')
<div class="flex flex-wrap">
	<div class="w-full">
		<div class="flex flex-wrap items-center justify-between mb-4">
			<h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">@yield('title')</h2>
			@if($mode == 'edit')
			<div class="flex gap-2 mt-2 md:mt-0">
				<div class="md:hidden">
					<button class="btn-secondary"
						type="button"
						x-data
						@click="$refs.relatedLinks.classList.toggle('hidden')">
						<i class="fa-solid fa-ellipsis-v"></i>
					</button>
				</div>
				<div x-ref="relatedLinks"
					class="hidden md:flex gap-2 w-full md:w-auto">
					<a class="btn-secondary text-sm show-as-dialog-link"
						href="{{ $U('/stockentries?embedded&product=') }}{{ $product->id }}"
						data-dialog-type="table">
						{{ $__t('Stock entries') }}
					</a>
					<a class="btn-secondary text-sm show-as-dialog-link"
						href="{{ $U('/stockjournal?embedded&product=') }}{{ $product->id }}"
						data-dialog-type="table">
						{{ $__t('Stock journal') }}
					</a>
				</div>
			</div>
			@endif
		</div>
	</div>
</div>

<hr class="my-4 border-gray-200 dark:border-gray-700">

<div class="flex flex-wrap -mx-2">
	<div class="w-full lg:w-1/2 px-2">
		<script>
			Grocy.EditMode = '{{ $mode }}';
		</script>

		@if($mode == 'edit')
		<script>
			Grocy.EditObjectId = {{ $product->id }};
		</script>

		@if(!empty($product->picture_file_name))
		<script>
			Grocy.ProductPictureFileName = '{{ $product->picture_file_name }}';
		</script>
		@endif
		@endif

		<form id="product-form"
			class="has-sticky-form-footer"
			novalidate>

			<div class="mb-4">
				<label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ $__t('Name') }}</label>
				<input type="text"
					class="input w-full"
					required
					id="name"
					name="name"
					value="@if($mode == 'edit'){{ $product->name}}@endif">
				<div class="invalid-feedback">{{ $__t('A name is required') }}</div>
			</div>

			<div class="mb-4">
				<div class="flex items-center gap-2">
					<input @if($mode=='create'
						)
						checked
						@elseif($mode=='edit'
						&&
						$product->active == 1) checked @endif class="w-4 h-4 text-primary-600 bg-gray-100 border-gray-300 rounded focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600" type="checkbox" id="active" name="active" value="1">
					<label class="text-sm text-gray-700 dark:text-gray-300"
						for="active">{{ $__t('Active') }}</label>
				</div>
			</div>

			@php $prefillById = ''; if($mode=='edit') { $prefillById = $product->parent_product_id; } @endphp
			@php
			$hint = '';
			if ($isSubProductOfOthers)
			{
			$hint = $__t('Not possible because this product is already used as a parent product in another product');
			}
			@endphp
			@include('components.productpicker', array(
			'products' => $products,
			'prefillById' => $prefillById,
			'disallowAllProductWorkflows' => true,
			'isRequired' => false,
			'label' => 'Parent product',
			'disabled' => $isSubProductOfOthers,
			'hint' => $hint
			))
			@php $hint = ''; @endphp

			<div class="mb-4">
				<label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ $__t('Description') }}</label>
				<textarea class="input w-full wysiwyg-editor"
					id="description"
					name="description">@if($mode == 'edit'){{ $product->description }}@endif</textarea>
			</div>

			@if(GROCY_FEATURE_FLAG_STOCK_LOCATION_TRACKING)
			<div class="mb-4">
				<label for="location_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ $__t('Default location') }}</label>
				<select required
					class="select w-full"
					id="location_id"
					name="location_id">
					<option></option>
					@foreach($locations as $location)
					<option @if($mode=='edit'
						&&
						$location->id == $product->location_id) selected="selected" @endif value="{{ $location->id }}">{{ $location->name }}</option>
					@endforeach
				</select>
				<div class="invalid-feedback">{{ $__t('A location is required') }}</div>
			</div>
			<div class="mb-4">
				<label for="default_consume_location_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
					{{ $__t('Default consume location') }}
					<i class="fa-solid fa-question-circle text-gray-500 dark:text-gray-400"
						data-tooltip
						title="{{ $__t('Stock entries at this location will be consumed first') }}"></i>
				</label>
				<select class="select w-full"
					id="default_consume_location_id"
					name="default_consume_location_id">
					<option></option>
					@foreach($locations as $location)
					<option @if($mode=='edit'
						&&
						$location->id == $product->default_consume_location_id) selected="selected" @endif value="{{ $location->id }}">{{ $location->name }}</option>
					@endforeach
				</select>

				@if(GROCY_FEATURE_FLAG_STOCK_PRODUCT_OPENED_TRACKING)
				<div class="flex items-center gap-2 mt-2">
					<input @if($mode=='edit'
						&&
						$product->move_on_open == 1) checked @endif class="w-4 h-4 text-primary-600 bg-gray-100 border-gray-300 rounded focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600" type="checkbox" id="move_on_open" name="move_on_open" value="1">
					<label class="text-sm text-gray-700 dark:text-gray-300"
						for="move_on_open">{{ $__t('Move on open') }}&nbsp;<i class="fa-solid fa-question-circle text-gray-500 dark:text-gray-400"
							data-tooltip
							title="{{$__t("When enabled, on marking this product as opened, the corresponding amount will be moved to the default consume location")}}"></i>
					</label>
				</div>
				@endif

			</div>
			@else
			<input type="hidden"
				name="location_id"
				id="location_id"
				value="1">
			<input type="hidden"
				name="default_consume_location_id"
				id="default_consume_location_id"
				value="1">
			@endif

			@php $prefillById = ''; if($mode=='edit') { $prefillById = $product->shopping_location_id; } @endphp
			@if(GROCY_FEATURE_FLAG_STOCK_PRICE_TRACKING)
			@include('components.shoppinglocationpicker', array(
			'label' => 'Default store',
			'prefillById' => $prefillById,
			'shoppinglocations' => $shoppinglocations
			))
			@else
			<input type="hidden"
				name="shopping_location_id"
				id="shopping_location_id"
				value="1">
			@endif

			@php if($mode == 'edit') { $value = $product->min_stock_amount; } else { $value = 0; } @endphp
			@include('components.numberpicker', array(
			'id' => 'min_stock_amount',
			'label' => 'Minimum stock amount',
			'min' => '0.',
			'decimals' => $userSettings['stock_decimal_places_amounts'],
			'value' => $value,
			'additionalGroupCssClasses' => 'mb-1',
			'additionalCssClasses' => 'locale-number-input locale-number-quantity-amount'
			))

			<div class="@if(GROCY_FEATURE_FLAG_STOCK_PRODUCT_OPENED_TRACKING) mb-2 @else mb-4 @endif">
				<div class="flex items-center gap-2">
					<input @if($mode=='edit'
						&&
						$product->cumulate_min_stock_amount_of_sub_products == 1) checked @endif class="w-4 h-4 text-primary-600 bg-gray-100 border-gray-300 rounded focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600" type="checkbox" id="cumulate_min_stock_amount_of_sub_products" name="cumulate_min_stock_amount_of_sub_products" value="1">
					<label class="text-sm text-gray-700 dark:text-gray-300"
						for="cumulate_min_stock_amount_of_sub_products">{{ $__t('Accumulate sub products min. stock amount') }}
						&nbsp;<i class="fa-solid fa-question-circle text-gray-500 dark:text-gray-400"
							data-tooltip
							title="{{ $__t('If enabled, the min. stock amount of sub products will be accumulated into this product, means the sub product will never be missing, only this product') }}"></i>
					</label>
				</div>
			</div>

			@if(GROCY_FEATURE_FLAG_STOCK_PRODUCT_OPENED_TRACKING)
			<div class="mb-4">
				<div class="flex items-center gap-2">
					<input @if($mode=='edit'
						&&
						$product->treat_opened_as_out_of_stock == 1) checked @endif class="w-4 h-4 text-primary-600 bg-gray-100 border-gray-300 rounded focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600" type="checkbox" id="treat_opened_as_out_of_stock" name="treat_opened_as_out_of_stock" value="1">
					<label class="text-sm text-gray-700 dark:text-gray-300"
						for="treat_opened_as_out_of_stock">{{ $__t('Treat opened as out of stock') }}&nbsp;<i class="fa-solid fa-question-circle text-gray-500 dark:text-gray-400"
							data-tooltip
							title="{{ $__t('When enabled, opened items will be counted as missing for calculating if this product is below its minimum stock amount') }}"></i>
					</label>
				</div>
			</div>
			@endif

			@if(GROCY_FEATURE_FLAG_STOCK_BEST_BEFORE_DATE_TRACKING)
			<div class="mb-4">
				<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
					for="location_id">{{ $__t('Due date type') }}
					<i class="fa-solid fa-question-circle text-gray-500 dark:text-gray-400"
						data-tooltip
						title="{{ $__t('Based on the selected type, the highlighting on the stock overview page will be different') }}"></i>
				</label>
				<div class="flex items-center gap-2 mb-1">
					<input class="w-4 h-4 text-primary-600 bg-gray-100 border-gray-300 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600"
						type="radio"
						name="due_type"
						id="due-type-bestbefore"
						value="1"
						@if($mode=='edit'
						&&
						$product->due_type == 1) checked @else checked @endif>
					<label class="text-sm text-gray-700 dark:text-gray-300"
						for="due-type-bestbefore">{{ $__t('Best before date') }}
						<i class="fa-solid fa-question-circle text-gray-500 dark:text-gray-400"
							data-tooltip
							title="{{ $__t('Means that the product is maybe still safe to be consumed after its due date is reached') }}"></i>
					</label>
				</div>
				<div class="flex items-center gap-2">
					<input class="w-4 h-4 text-primary-600 bg-gray-100 border-gray-300 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600"
						type="radio"
						name="due_type"
						id="due-type-expiration"
						value="2"
						@if($mode=='edit'
						&&
						$product->due_type == 2) checked @endif>
					<label class="text-sm text-gray-700 dark:text-gray-300"
						for="due-type-expiration">{{ $__t('Expiration date') }}
						<i class="fa-solid fa-question-circle text-gray-500 dark:text-gray-400"
							data-tooltip
							title="{{ $__t('Means that the product is not safe to be consumed after its due date is reached') }}"></i>
					</label>
				</div>
			</div>

			@php if($mode == 'edit') { $value = $product->default_best_before_days; } else { $value = 0; } @endphp
			@include('components.numberpicker', array(
			'id' => 'default_best_before_days',
			'label' => 'Default due days',
			'min' => -1,
			'value' => $value,
			'hint' => $__t('For purchases this amount of days will be added to today for the due date suggestion') . ' (' . $__t('-1 means that this product will be never overdue') . ')'
			))

			@if(GROCY_FEATURE_FLAG_STOCK_PRODUCT_OPENED_TRACKING)
			@php if($mode == 'edit') { $value = $product->default_best_before_days_after_open; } else { $value = 0; } @endphp
			@include('components.numberpicker', array(
			'id' => 'default_best_before_days_after_open',
			'label' => 'Default due days after opened',
			'min' => 0,
			'value' => $value,
			'hint' => $__t('When this product was marked as opened, the due date will be replaced by today + this amount of days, but only if the resulting date is not after the original due date (a value of 0 disables this)')
			))
			@else
			<input type="hidden"
				name="default_best_before_days_after_open"
				id="default_best_before_days_after_open"
				value="1">
			@endif
			@else
			<input type="hidden"
				name="default_best_before_days"
				id="default_best_before_days"
				value="1">
			<input type="hidden"
				name="due_type"
				id="due_type"
				value="1">
			@endif

			@if(GROCY_FEATURE_FLAG_STOCK_PRODUCT_FREEZING)
			@php if($mode == 'edit') { $value = $product->default_best_before_days_after_freezing; } else { $value = 0; } @endphp
			@include('components.numberpicker', array(
			'id' => 'default_best_before_days_after_freezing',
			'label' => 'Default due days after freezing',
			'min' => -1,
			'value' => $value,
			'hint' => $__t('On moving this product to a freezer location (so when freezing it), the due date will be replaced by today + this amount of days') . ' (' . $__t('-1 means that this product will be never overdue') . ')'
			))

			@php if($mode == 'edit') { $value = $product->default_best_before_days_after_thawing; } else { $value = 0; } @endphp
			@include('components.numberpicker', array(
			'id' => 'default_best_before_days_after_thawing',
			'label' => 'Default due days after thawing',
			'min' => 0,
			'value' => $value,
			'hint' => $__t('On moving this product from a freezer location (so when thawing it), the due date will be replaced by today + this amount of days')
			))

			<div class="mb-4">
				<div class="flex items-center gap-2">
					<input @if($mode=='edit'
						&&
						$product->should_not_be_frozen == 1) checked @endif class="w-4 h-4 text-primary-600 bg-gray-100 border-gray-300 rounded focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600" type="checkbox" id="should_not_be_frozen" name="should_not_be_frozen" value="1">
					<label class="text-sm text-gray-700 dark:text-gray-300"
						for="should_not_be_frozen">{{ $__t('Should not be frozen') }}&nbsp;<i class="fa-solid fa-question-circle text-gray-500 dark:text-gray-400"
							data-tooltip
							title="{{ $__t('When enabled, on moving this product to a freezer location (so when freezing it), a warning will be shown') }}"></i>
					</label>
				</div>
			</div>
			@else
			<input type="hidden"
				name="default_best_before_days_after_freezing"
				value="0">
			<input type="hidden"
				name="default_best_before_days_after_thawing"
				value="0">
			<input type="hidden"
				name="should_not_be_frozen"
				value="0">
			@endif

			<div class="mb-4">
				<label for="product_group_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ $__t('Product group') }}</label>
				<select class="select w-full"
					id="product_group_id"
					name="product_group_id">
					<option></option>
					@foreach($productgroups as $productgroup)
					<option @if($mode=='edit'
						&&
						$productgroup->id == $product->product_group_id) selected="selected" @endif value="{{ $productgroup->id }}">{{ $productgroup->name }}</option>
					@endforeach
				</select>
			</div>

			<div class="mb-4">
				<label for="qu_id_stock" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ $__t('Quantity unit stock') }}</label>
				<select required
					class="select w-full input-group-qu"
					id="qu_id_stock"
					name="qu_id_stock">
					<option></option>
					@foreach($quantityunitsStock as $quantityunit)
					<option @if($mode=='edit'
						&&
						$quantityunit->id == $product->qu_id_stock) selected="selected" @endif value="{{ $quantityunit->id }}" data-plural-form="{{ $quantityunit->name_plural }}">{{ $quantityunit->name }}</option>
					@endforeach
				</select>
				<div class="invalid-feedback">{{ $__t('A quantity unit is required') }}</div>
			</div>

			<div class="mb-4">
				<label for="qu_id_purchase" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ $__t('Default quantity unit purchase') }}
				<i class="fa-solid fa-question-circle text-gray-500 dark:text-gray-400"
					data-tooltip
					title="{{ $__t('This is the default quantity unit used on purchase and when adding this product to the shopping list') }}"></i>
				</label>
				<select required
					class="select w-full input-group-qu"
					id="qu_id_purchase"
					name="qu_id_purchase">
					<option></option>
					@foreach($referencedQuantityunits as $quantityunit)
					<option @if($mode=='edit'
						&&
						$quantityunit->id == $product->qu_id_purchase) selected="selected" @endif value="{{ $quantityunit->id }}">{{ $quantityunit->name }}</option>
					@endforeach
				</select>
				<div class="invalid-feedback">{{ $__t('A quantity unit is required') }}</div>
			</div>

			<div class="mb-4">
				<label for="qu_id_consume" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ $__t('Default quantity unit consume') }}
				<i class="fa-solid fa-question-circle text-gray-500 dark:text-gray-400"
					data-tooltip
					title="{{ $__t('This is the default quantity unit used when consuming this product') }}"></i>
				</label>
				<select required
					class="select w-full input-group-qu"
					id="qu_id_consume"
					name="qu_id_consume">
					<option></option>
					@foreach($referencedQuantityunits as $quantityunit)
					<option @if($mode=='edit'
						&&
						$quantityunit->id == $product->qu_id_consume) selected="selected" @endif value="{{ $quantityunit->id }}">{{ $quantityunit->name }}</option>
					@endforeach
				</select>
				<div class="invalid-feedback">{{ $__t('A quantity unit is required') }}</div>
			</div>

			<div class="mb-4">
				<label for="qu_id_price" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ $__t('Quantity unit for prices') }}
				<i class="fa-solid fa-question-circle text-gray-500 dark:text-gray-400"
					data-tooltip
					title="{{ $__t('When displaying prices for this product, they will be related to this quantity unit') }}"></i>
				</label>
				<select required
					class="select w-full input-group-qu"
					id="qu_id_price"
					name="qu_id_price">
					<option></option>
					@foreach($referencedQuantityunits as $quantityunit)
					<option @if($mode=='edit'
						&&
						$quantityunit->id == $product->qu_id_price) selected="selected" @endif value="{{ $quantityunit->id }}">{{ $quantityunit->name }}</option>
					@endforeach
				</select>
				<div class="invalid-feedback">{{ $__t('A quantity unit is required') }}</div>
			</div>

			<div class="mb-2">
				<div class="flex items-center gap-2">
					<input @if($mode=='edit'
						&&
						$product->enable_tare_weight_handling == 1) checked @endif class="w-4 h-4 text-primary-600 bg-gray-100 border-gray-300 rounded focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600" type="checkbox" id="enable_tare_weight_handling" name="enable_tare_weight_handling" value="1">
					<label class="text-sm text-gray-700 dark:text-gray-300"
						for="enable_tare_weight_handling">{{ $__t('Enable tare weight handling') }}
						&nbsp;<i class="fa-solid fa-question-circle text-gray-500 dark:text-gray-400"
							data-tooltip
							title="{{ $__t('This is useful e.g. for flour in jars - on purchase/consume/inventory you always weigh the whole jar, the amount to be posted is then automatically calculated based on what is in stock and the tare weight defined below') }}"></i>
					</label>
				</div>
			</div>

			@php if($mode == 'edit') { $value = $product->tare_weight; } else { $value = 0; } @endphp
			@php if(($mode == 'edit' && $product->enable_tare_weight_handling == 0) || $mode == 'create') { $additionalAttributes = 'disabled'; } else { $additionalAttributes = ''; } @endphp
			@include('components.numberpicker', array(
			'id' => 'tare_weight',
			'label' => 'Tare weight',
			'min' => 0,
			'decimals' => $userSettings['stock_decimal_places_amounts'],
			'value' => $value,
			'additionalAttributes' => $additionalAttributes,
			'contextInfoId' => 'tare_weight_qu_info',
			'additionalCssClasses' => 'locale-number-input locale-number-quantity-amount'
			))
			@php $additionalAttributes = '' @endphp

			@if(GROCY_FEATURE_FLAG_RECIPES)
			<div class="mb-4">
				<div class="flex items-center gap-2">
					<input @if($mode=='edit'
						&&
						$product->not_check_stock_fulfillment_for_recipes == 1) checked @endif class="w-4 h-4 text-primary-600 bg-gray-100 border-gray-300 rounded focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600" type="checkbox" id="not_check_stock_fulfillment_for_recipes" name="not_check_stock_fulfillment_for_recipes" value="1">
					<label class="text-sm text-gray-700 dark:text-gray-300"
						for="not_check_stock_fulfillment_for_recipes">{{ $__t('Disable stock fulfillment checking for this ingredient') }}
						&nbsp;<i class="fa-solid fa-question-circle text-gray-500 dark:text-gray-400"
							data-tooltip
							title="{{ $__t('This will be used as the default setting when adding this product as a recipe ingredient') }}"></i>
					</label>
				</div>
			</div>
			@else
			<input type="hidden"
				name="not_check_stock_fulfillment_for_recipes"
				id="not_check_stock_fulfillment_for_recipes"
				value="0">
			@endif

			@php if($mode == 'edit') { $value = $product->calories; } else { $value = 0; } @endphp
			@include('components.numberpicker', array(
			'id' => 'calories',
			'label' => 'Energy',
			'min' => '0.' . str_repeat('0', $userSettings['stock_decimal_places_amounts']),
			'decimals' => $userSettings['stock_decimal_places_amounts'],
			'value' => $value,
			'hint' => $__t('Per stock quantity unit'),
			'contextInfoId' => 'energy_qu_info',
			'isRequired' => false,
			'additionalCssClasses' => 'locale-number-input locale-number-quantity-amount'
			))

			@php if($mode == 'edit') { $value = $product->quick_consume_amount; } else { $value = 1; } @endphp
			@include('components.numberpicker', array(
			'id' => 'quick_consume_amount',
			'label' => 'Quick consume amount',
			'min' => $DEFAULT_MIN_AMOUNT,
			'decimals' => $userSettings['stock_decimal_places_amounts'],
			'value' => $value,
			'hint' => $__t('This amount is used for the "quick consume button" on the stock overview page (related to quantity unit stock)'),
			'contextInfoId' => 'quick_consume_qu_info',
			'additionalCssClasses' => 'locale-number-input locale-number-quantity-amount'
			))

			@php if($mode == 'edit') { $value = $product->quick_open_amount; } else { $value = 1; } @endphp
			@include('components.numberpicker', array(
			'id' => 'quick_open_amount',
			'label' => 'Quick open amount',
			'min' => $DEFAULT_MIN_AMOUNT,
			'decimals' => $userSettings['stock_decimal_places_amounts'],
			'value' => $value,
			'hint' => $__t('This amount is used for the "quick open button" on the stock overview page (related to quantity unit stock)'),
			'contextInfoId' => 'quick_open_qu_info',
			'additionalCssClasses' => 'locale-number-input locale-number-quantity-amount'
			))

			@if(GROCY_FEATURE_FLAG_LABEL_PRINTER)
			<div class="mb-4">
				<label for="default_stock_label_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ $__t('Default stock entry label') }}
				<i class="fa-solid fa-question-circle text-gray-500 dark:text-gray-400"
					data-tooltip
					title="{{ $__t('This is the default which will be prefilled on purchase') }}"></i>
				</label>
				<select class="select w-full"
					id="default_stock_label_type"
					name="default_stock_label_type">
					<option @if($mode=='edit'
						&&
						$product->default_stock_label_type == 0 ) selected="selected" @endif value="0">{{ $__t('No label') }}</option>
					<option @if($mode=='edit'
						&&
						$product->default_stock_label_type == 1 ) selected="selected" @endif value="1">{{ $__t('Single label') }}</option>
					<option @if($mode=='edit'
						&&
						$product->default_stock_label_type == 2 ) selected="selected" @endif value="2">{{ $__t('Label per unit') }}</option>
				</select>
			</div>

			<div class="mb-4">
				<div class="flex items-center gap-2">
					<input @if($mode=='edit'
						&&
						$product->auto_reprint_stock_label == 1) checked @endif class="w-4 h-4 text-primary-600 bg-gray-100 border-gray-300 rounded focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600" type="checkbox" id="auto_reprint_stock_label" name="auto_reprint_stock_label" value="1">
					<label class="text-sm text-gray-700 dark:text-gray-300"
						for="auto_reprint_stock_label">{{ $__t('Auto reprint stock entry label') }}&nbsp;<i class="fa-solid fa-question-circle text-gray-500 dark:text-gray-400"
							data-tooltip
							title="{{ $__t('When enabled, auto-changing the due date of a stock entry (by opening/freezing/thawing and having corresponding default due days set) will reprint its label') }}"></i>
					</label>
				</div>
			</div>
			@endif

			@if(GROCY_FEATURE_FLAG_STOCK_PRICE_TRACKING)
			<div class="mb-4">
				<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
					for="default_purchase_price_type">{{ $__t('Default purchase price type') }}
					<i class="fa-solid fa-question-circle text-gray-500 dark:text-gray-400"
						data-tooltip
						title="{{ $__t('This will be used as the default price type selection on purchase') }}"></i>
				</label>
				<div class="flex items-center gap-2 mb-1">
					<input class="w-4 h-4 text-primary-600 bg-gray-100 border-gray-300 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600"
						type="radio"
						name="default_purchase_price_type"
						id="default-purchase-price-type-unspecified"
						value="1"
						@if($mode=='edit'
						&&
						$product->default_purchase_price_type == 1) checked @else checked @endif>
					<label class="text-sm text-gray-700 dark:text-gray-300"
						for="default-purchase-price-type-unspecified">{{ $__t('Unspecified') }}
					</label>
				</div>
				<div class="flex items-center gap-2 mb-1">
					<input class="w-4 h-4 text-primary-600 bg-gray-100 border-gray-300 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600"
						type="radio"
						name="default_purchase_price_type"
						id="default-purchase-price-type-unit-price"
						value="2"
						@if($mode=='edit'
						&&
						$product->default_purchase_price_type == 2) checked @endif>
					<label class="text-sm text-gray-700 dark:text-gray-300"
						for="default-purchase-price-type-unit-price">{{ $__t('Unit price') }}
					</label>
				</div>
				<div class="flex items-center gap-2">
					<input class="w-4 h-4 text-primary-600 bg-gray-100 border-gray-300 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600"
						type="radio"
						name="default_purchase_price_type"
						id="default-purchase-price-type-total-price"
						value="3"
						@if($mode=='edit'
						&&
						$product->default_purchase_price_type == 3) checked @endif>
					<label class="text-sm text-gray-700 dark:text-gray-300"
						for="default-purchase-price-type-total-price">{{ $__t('Total price') }}
					</label>
				</div>
			</div>
			@endif

			@if(GROCY_FEATURE_FLAG_STOCK_PRODUCT_OPENED_TRACKING)
			<div class="mb-4">
				<div class="flex items-center gap-2">
					<input @if($mode=='edit'
						&&
						$product->disable_open == 1) checked @endif class="w-4 h-4 text-primary-600 bg-gray-100 border-gray-300 rounded focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600" type="checkbox" id="disable_open" name="disable_open" value="1">
					<label class="text-sm text-gray-700 dark:text-gray-300"
						for="disable_open">{{ $__t('Can\'t be opened') }}
					</label>
				</div>
			</div>
			@endif

			<div class="mb-4">
				<div class="flex items-center gap-2">
					<input @if($mode=='edit'
						&&
						$product->hide_on_stock_overview == 1) checked @endif class="w-4 h-4 text-primary-600 bg-gray-100 border-gray-300 rounded focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600" type="checkbox" id="hide_on_stock_overview" name="hide_on_stock_overview" value="1">
					<label class="text-sm text-gray-700 dark:text-gray-300"
						for="hide_on_stock_overview">{{ $__t('Never show on stock overview') }}&nbsp;<i class="fa-solid fa-question-circle text-gray-500 dark:text-gray-400"
							data-tooltip
							title="{{ $__t('The stock overview page lists all products which are currently in stock or below their min. stock amount - enable this to hide this product there always') }}"></i>
					</label>
				</div>
			</div>

			<div class="mb-4">
				<div class="flex items-center gap-2">
					<input @if($mode=='edit'
						&&
						$product->no_own_stock == 1) checked @endif class="w-4 h-4 text-primary-600 bg-gray-100 border-gray-300 rounded focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600" type="checkbox" id="no_own_stock" name="no_own_stock" value="1">
					<label class="text-sm text-gray-700 dark:text-gray-300"
						for="no_own_stock">{{ $__t('Disable own stock') }}&nbsp;<i class="fa-solid fa-question-circle text-gray-500 dark:text-gray-400"
							data-tooltip
							title="{{ $__t('When enabled, this product can\'t have own stock, means it will not be selectable on purchase (useful for parent products which are just used as a summary/total view of the child products)') }}"></i>
					</label>
				</div>
			</div>

			@include('components.userfieldsform', array(
			'userfields' => $userfields,
			'entity' => 'products'
			))

			<div class="py-5"></div>
			<div class="sticky-form-footer pt-1">
				<small id="save-hint"
					class="my-1 text-sm text-gray-500 dark:text-gray-400 @if($mode == 'edit') hidden @endif">{{ $__t('Save & continue to add quantity unit conversions & barcodes') }}</small>

				<button id="save-product-button"
					class="save-product-button btn-success mb-2 default-submit-button"
					data-location="continue">{{ $__t('Save & continue') }}</button>
				<button class="save-product-button btn-primary mb-2"
					data-location="return">{{ $__t('Save & return to products') }}</button>
			</div>
		</form>

	</div>

	<div class="w-full lg:w-1/2 px-2">

		<div class="@if($mode == 'create' || !GROCY_FEATURE_FLAG_STOCK) hidden @endif">
			<div class="w-full">
				<div class="flex flex-wrap items-center justify-between mb-4">
					<h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
						{{ $__t('Barcodes') }}
					</h4>
					<div class="flex gap-2 mt-2 md:mt-0">
						<div class="md:hidden">
							<button class="btn-secondary"
								type="button"
								x-data
								@click="$refs.barcodeLinks.classList.toggle('hidden')">
								<i class="fa-solid fa-ellipsis-v"></i>
							</button>
						</div>
						@if($mode == "edit")
						<div x-ref="barcodeLinks"
							class="hidden md:flex gap-2 w-full md:w-auto">
							<a class="btn-primary text-sm show-as-dialog-link"
								href="{{ $U('/productbarcodes/new?embedded&product=' . $product->id ) }}">
								{{ $__t('Add') }}
							</a>
						</div>
						@endif
					</div>
				</div>

				<h5 id="barcode-headline-info"
					class="text-gray-500 dark:text-gray-400 italic text-sm"></h5>

				<table id="barcode-table"
					class="table table-sm table-striped nowrap w-100">
					<thead>
						<tr>
							<th class="border-r"><a class="text-gray-500 dark:text-gray-400 change-table-columns-visibility-button"
									data-tooltip
									title="{{ $__t('Table options') }}"
									data-table-selector="#barcode-table"
									href="#"><i class="fa-solid fa-eye"></i></a>
							</th>
							<th>{{ $__t('Barcode') }}</th>
							<th class="@if(!GROCY_FEATURE_FLAG_STOCK_PRICE_TRACKING) hidden @endif allow-grouping">{{ $__t('Store') }}</th>
							<th class="allow-grouping">{{ $__t('Quantity unit') }}</th>
							<th>{{ $__t('Amount') }}</th>
							<th class="@if(!GROCY_FEATURE_FLAG_STOCK_PRICE_TRACKING) hidden @endif">{{ $__t('Last price') }}</th>
							<th>{{ $__t('Note') }}</th>

							@include('components.userfields_thead', array(
							'userfields' => $productBarcodeUserfields
							))
						</tr>
					</thead>
					<tbody class="hidden">
						@if($mode == "edit")
						@foreach($barcodes as $barcode)
						@if($barcode->product_id == $product->id || $barcode->product_id == null)
						<tr>
							<td class="fit-content border-r">
								<a class="btn-primary text-sm show-as-dialog-link @if($barcode->product_id == null) opacity-50 pointer-events-none @endif"
									href="{{ $U('/productbarcodes/' . $barcode->id . '?embedded&product=' . $product->id ) }}">
									<i class="fa-solid fa-edit"></i>
								</a>
								<a class="btn-danger text-sm barcode-delete-button @if($barcode->product_id == null) opacity-50 pointer-events-none @endif"
									href="#"
									data-barcode-id="{{ $barcode->id }}"
									data-barcode="{{ $barcode->barcode }}"
									data-product-barcode="{{ $product->barcode }}"
									data-product-id="{{ $product->id }}">
									<i class="fa-solid fa-trash"></i>
								</a>
							</td>
							<td>
								{{ $barcode->barcode }}
							</td>
							<td class="@if(!GROCY_FEATURE_FLAG_STOCK_PRICE_TRACKING) hidden @endif"
								id="barcode-shopping-location">
								@if (FindObjectInArrayByPropertyValue($shoppinglocations, 'id', $barcode->shopping_location_id) !== null)
								{{ FindObjectInArrayByPropertyValue($shoppinglocations, 'id', $barcode->shopping_location_id)->name }}
								@endif
							</td>
							<td>
								@if(!empty($barcode->qu_id))
								{{ FindObjectInArrayByPropertyValue($quantityunits, 'id', $barcode->qu_id)->name }}
								@endif
							</td>
							<td>
								@if(!empty($barcode->amount))
								<span class="locale-number locale-number-quantity-amount">{{ $barcode->amount }}</span>
								@endif
							</td>
							<td class="@if(!GROCY_FEATURE_FLAG_STOCK_PRICE_TRACKING) hidden @endif">
								<span class="locale-number locale-number-currency">{{ $barcode->last_price }}</span>
							</td>
							<td>
								{{ $barcode->note }}
							</td>

							@include('components.userfields_tbody', array(
							'userfields' => $productBarcodeUserfields,
							'userfieldValues' => FindAllObjectsInArrayByPropertyValue($productBarcodeUserfieldValues, 'object_id', $barcode->id)
							))
						</tr>
						@endif
						@endforeach
						@endif
					</tbody>
				</table>
			</div>
		</div>

		<div class="mt-4 @if($mode == 'create') hidden @endif">
			<div class="w-full">
				<div class="mb-4">
					<h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">
						<span class="tracking-tight">{{ $__t('Grocycode') }}</span>
						<i class="fa-solid fa-question-circle text-gray-500 dark:text-gray-400"
							data-tooltip
							title="{{ $__t('Grocycode is a unique referer to this %s in your Grocy instance - print it onto a label and scan it like any other barcode', $__t('Product')) }}"></i>
					</h4>
					<p class="mb-2">
						@if($mode == 'edit')
						<img src="{{ $U('/product/' . $product->id . '/grocycode?size=60') }}"
							class="lg:float-left"
							loading="lazy">
						@endif
					</p>
					<p class="flex gap-2">
						<a class="btn-secondary text-sm"
							href="{{ $U('/product/' . $product->id . '/grocycode?download=true') }}">{{ $__t('Download') }}</a>
						@if(GROCY_FEATURE_FLAG_LABEL_PRINTER)
						<a class="btn-secondary text-sm product-grocycode-label-print"
							data-product-id="{{ $product->id }}"
							href="#">
							{{ $__t('Print on label printer') }}
						</a>
						@endif
					</p>
				</div>
			</div>
		</div>

		<div class="@if(GROCY_FEATURE_FLAG_STOCK) mt-8 @endif @if($mode == 'create') hidden @endif">
			<div class="w-full">
				<div class="flex flex-wrap items-center justify-between mb-4">
					<h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
						{{ $__t('Product specific QU conversions') }}
					</h4>
					<div class="flex gap-2 mt-2 md:mt-0">
						<div class="md:hidden">
							<button class="btn-secondary"
								type="button"
								x-data
								@click="$refs.quLinks.classList.toggle('hidden')">
								<i class="fa-solid fa-ellipsis-v"></i>
							</button>
						</div>
						@if($mode == "edit")
						<div x-ref="quLinks"
							class="hidden md:flex gap-2 w-full md:w-auto">
							<a class="btn-primary text-sm show-as-dialog-link"
								href="{{ $U('/quantityunitconversion/new?embedded&product=' . $product->id ) }}"
								data-dialog-type="wider">
								{{ $__t('Add') }}
							</a>
							<a class="btn-secondary text-sm show-as-dialog-link"
								href="{{ $U('/quantityunitconversionsresolved?embedded&product=' . $product->id ) }}"
								data-dialog-type="wider"
								data-tooltip
								title="{{ $__t('This shows all to this product directly or indirectly related quantity units and their derived conversion factors') }}">
								{{ $__t('Show resolved conversions') }}
							</a>
						</div>
						@endif
					</div>
				</div>

				<table id="qu-conversions-table-products"
					class="table table-sm table-striped nowrap w-100">
					<thead>
						<tr>
							<th class="border-r"><a class="text-gray-500 dark:text-gray-400 change-table-columns-visibility-button"
									data-tooltip
									title="{{ $__t('Table options') }}"
									data-table-selector="#qu-conversions-table-products"
									href="#"><i class="fa-solid fa-eye"></i></a>
							</th>
							<th class="allow-grouping">{{ $__t('Quantity unit from') }}</th>
							<th class="allow-grouping">{{ $__t('Quantity unit to') }}</th>
							<th>{{ $__t('Factor') }}</th>
							<th></th>
						</tr>
					</thead>
					<tbody class="hidden">
						@if($mode == "edit")
						@foreach($quConversions as $quConversion)
						<tr>
							<td class="fit-content border-r">
								<a class="btn-primary text-sm show-as-dialog-link @if($quConversion->product_id == null) opacity-50 pointer-events-none @endif"
									href="{{ $U('/quantityunitconversion/' . $quConversion->id . '?embedded&product=' . $product->id ) }}"
									data-dialog-type="wider">
									<i class="fa-solid fa-edit"></i>
								</a>
								<a class="btn-danger text-sm qu-conversion-delete-button @if($quConversion->product_id == null) opacity-50 pointer-events-none @endif"
									href="#"
									data-qu-conversion-id="{{ $quConversion->id }}">
									<i class="fa-solid fa-trash"></i>
								</a>
							</td>
							<td>
								{{ FindObjectInArrayByPropertyValue($quantityunits, 'id', $quConversion->from_qu_id)->name }}
							</td>
							<td>
								{{ FindObjectInArrayByPropertyValue($quantityunits, 'id', $quConversion->to_qu_id)->name }}
							</td>
							<td>
								<span class="locale-number locale-number-quantity-amount">{{ $quConversion->factor }}</span>
							</td>
							<td class="italic">
								{!! $__t('This means 1 %1$s is the same as %2$s %3$s', FindObjectInArrayByPropertyValue($quantityunits, 'id', $quConversion->from_qu_id)->name, '<span class="locale-number locale-number-quantity-amount">' . $quConversion->factor . '</span>', $__n($quConversion->factor, FindObjectInArrayByPropertyValue($quantityunits, 'id', $quConversion->to_qu_id)->name, FindObjectInArrayByPropertyValue($quantityunits, 'id', $quConversion->to_qu_id)->name_plural, true)) !!}
							</td>
						</tr>
						@endforeach
						@endif
					</tbody>
				</table>
			</div>
		</div>

		<div class="@if($mode == 'edit') mt-8 @endif">
			<div class="w-full">
				<div class="mb-4">
					<h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
						{{ $__t('Picture') }}
					</h4>
					<div class="w-full lg:w-3/4">
						<div class="flex gap-2">
							<div class="flex-1">
								<input type="file"
									class="block w-full text-sm text-gray-500 dark:text-gray-400
									file:mr-4 file:py-2 file:px-4
									file:rounded file:border-0
									file:text-sm file:font-medium
									file:bg-primary-50 file:text-primary-700
									hover:file:bg-primary-100
									dark:file:bg-gray-700 dark:file:text-gray-300"
									id="product-picture"
									accept="image/*">
								<label id="product-picture-label"
									class="text-sm text-gray-700 dark:text-gray-300 mt-1 block @if(empty($product->picture_file_name)) hidden @endif"
									for="product-picture">
									{{ $product->picture_file_name }}
								</label>
								<label id="product-picture-label-none"
									class="text-sm text-gray-500 dark:text-gray-400 mt-1 block @if(!empty($product->picture_file_name)) hidden @endif"
									for="product-picture">
									{{ $__t('No file selected') }}
								</label>
							</div>
							<button type="button" class="px-3 py-2 text-gray-600 dark:text-gray-400 hover:text-danger hover:dark:text-danger">
								<i class="fa-solid fa-trash"
									id="delete-current-product-picture-button"></i>
							</button>
						</div>
					</div>
				</div>
				@if($mode == "edit" && !empty($product->picture_file_name))
				<img id="current-product-picture"
					src="{{ $U('/api/files/productpictures/' . base64_encode($product->picture_file_name) . '?force_serve_as=picture&best_fit_width=400') }}"
					class="max-w-full rounded-lg border border-gray-200 dark:border-gray-700 mt-2 mb-5"
					loading="lazy">
				<p id="delete-current-product-picture-on-save-hint"
					class="text-sm text-gray-500 dark:text-gray-400 italic hidden pb-5">{{ $__t('The current picture will be deleted on save') }}</p>
				@else
				<p id="no-current-product-picture-hint"
					class="text-sm text-gray-500 dark:text-gray-400 italic pb-5">{{ $__t('No picture available') }}</p>
				@endif
			</div>
		</div>
	</div>
</div>
@stop
