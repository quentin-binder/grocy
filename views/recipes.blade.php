@php require_frontend_packages(['datatables']); @endphp

@extends('layout.default')

@section('title', $__t('Recipes'))

@push('pageStyles')
<style>
	.card-img-top {
		max-height: 250px !important;
		object-fit: cover !important;
	}

	.card-columns {
		column-count: 1;
	}

	@media (min-width: 768px) {
		.card-columns {
			column-count: 2;
		}
	}
</style>
@endpush

@section('content')
<script>
	Grocy.QuantityUnits = {!! json_encode($quantityUnits) !!};
	Grocy.QuantityUnitConversionsResolved = {!! json_encode($quantityUnitConversionsResolved) !!};
</script>

<div class="flex flex-wrap -mx-2">
	<div class="@if(boolval($userSettings['recipes_show_list_side_by_side']) || $embedded) w-full md:w-1/2 @else w-full @endif px-2 print:hidden" x-data="{ showFilters: false, showLinks: false }">
		<div class="border-b border-gray-200 dark:border-gray-700 mb-2 py-1">
			<div class="flex justify-between items-start">
				<h2 class="text-2xl font-bold">@yield('title')</h2>
				<div class="flex gap-2 @if($embedded) pr-5 @endif">
					<button class="btn-ghost md:hidden mt-2"
						type="button"
						@click="showFilters = !showFilters">
						<i class="fa-solid fa-filter"></i>
					</button>
					<button class="btn-ghost md:hidden mt-2"
						type="button"
						@click="showLinks = !showLinks">
						<i class="fa-solid fa-ellipsis-v"></i>
					</button>
				</div>
			</div>
			<div x-show="showLinks"
				x-collapse
				class="md:flex md:justify-end mt-2 md:mt-0">
				<a class="btn-primary w-full md:w-auto"
					href="{{ $U('/recipe/new') }}">
					{{ $__t('Add') }}
				</a>
			</div>
		</div>

		<div x-show="showFilters"
			x-collapse
			class="md:flex flex-wrap gap-3 mb-3"
			id="table-filter-row">
			<div class="w-full md:w-auto md:flex-1">
				<div class="flex items-center gap-2">
					<span class="px-3 py-2 bg-gray-100 dark:bg-gray-800 rounded-l border border-r-0 border-gray-300 dark:border-gray-600">
						<i class="fa-solid fa-search"></i>
					</span>
					<input type="text"
						id="search"
						class="input flex-1 rounded-l-none"
						placeholder="{{ $__t('Search') }}">
				</div>
			</div>

			<div class="w-full md:w-auto md:flex-1">
				<div class="flex items-center gap-2">
					<span class="px-3 py-2 bg-gray-100 dark:bg-gray-800 rounded-l border border-r-0 border-gray-300 dark:border-gray-600">
						<i class="fa-solid fa-filter"></i>&nbsp;{{ $__t('Status') }}
					</span>
					<select class="select flex-1 rounded-l-none"
						id="status-filter">
						<option value="all">{{ $__t('All') }}</option>
						<option value="Xenoughinstock">{{ $__t('Enough in stock') }}</option>
						<option value="enoughinstockwithshoppinglist">{{ $__t('Not enough in stock, but already on the shopping list') }}</option>
						<option value="notenoughinstock">{{ $__t('Not enough in stock') }}</option>
					</select>
				</div>
			</div>

			<div class="w-full md:w-auto flex justify-end items-start">
				<button id="clear-filter-button"
					class="btn-ghost text-sm px-3 py-2"
					data-tooltip="{{ $__t('Clear filter') }}">
					<i class="fa-solid fa-filter-circle-xmark"></i>
				</button>
			</div>
		</div>

		<div x-data="{ activeTab: 'list' }" class="w-full">
			<ul class="flex border-b border-gray-200 dark:border-gray-700 mb-3">
				<li>
					<button @click="activeTab = 'list'"
						:class="activeTab === 'list' ? 'border-b-2 border-primary-500 text-primary-600 dark:text-primary-400' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300'"
						class="px-4 py-2 -mb-px font-medium"
						id="list-tab">{{ $__t('List') }}</button>
				</li>
				<li>
					<button @click="activeTab = 'gallery'"
						:class="activeTab === 'gallery' ? 'border-b-2 border-primary-500 text-primary-600 dark:text-primary-400' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300'"
						class="px-4 py-2 -mb-px font-medium"
						id="gallery-tab">{{ $__t('Gallery') }}</button>
				</li>
			</ul>

			<div>
				<div x-show="activeTab === 'list'"
					id="list">
					<table id="recipes-table"
						class="w-full">
					<thead>
						<tr>
							<th class="border-r border-gray-300 dark:border-gray-600">
								<a class="text-gray-500 dark:text-gray-400 change-table-columns-visibility-button"
									data-tooltip="{{ $__t('Table options') }}"
									data-table-selector="#recipes-table"
									href="#">
									<i class="fa-solid fa-eye"></i>
								</a>
							</th>
							<th>{{ $__t('Name') }}</th>
							<th class="allow-grouping">{{ $__t('Desired servings') }}</th>
							<th class="allow-grouping">
								{{ $__t('Due score') }}
								<i class="fa-solid fa-question-circle text-gray-500 dark:text-gray-400 text-sm"
									data-tooltip="{{ $__t('The higher this number is, the more ingredients currently in stock are due soon, overdue or already expired') }}"></i>
							</th>
							<th data-shadow-rowgroup-column="8"
								class="@if(!GROCY_FEATURE_FLAG_STOCK) hidden @endif allow-grouping">{{ $__t('Requirements fulfilled') }}</th>
							<th class="hidden">Hidden status for sorting of "Requirements fulfilled" column</th>
							<th class="hidden">Hidden status for filtering by status</th>
							<th class="hidden">Hidden recipe ingredient product names</th>
							<th class="hidden">Hidden status for grouping by status</th>

							@include('components.userfields_thead', array(
							'userfields' => $userfields
							))

						</tr>
					</thead>
					<tbody class="hidden">
						@foreach($recipes as $recipe)
						<tr id="recipe-row-{{ $recipe->id }}"
							data-recipe-id="{{ $recipe->id }}">
							<td class="whitespace-nowrap border-r border-gray-300 dark:border-gray-600">
								<a class="btn-primary text-sm px-2 py-1 hide-when-embedded hide-on-fullscreen-card recipe-edit-button"
									href="{{ $U('/recipe/') }}{{ $recipe->id }}"
									data-tooltip="{{ $__t('Edit this item') }}">
									<i class="fa-solid fa-edit"></i>
								</a>
								<div class="inline-block" x-data="{ open: false }">
									<button class="btn-ghost text-sm px-2 py-1"
										type="button"
										@click="open = !open">
										<i class="fa-solid fa-ellipsis-v"></i>
									</button>
									<div x-show="open"
										@click.away="open = false"
										class="absolute right-0 mt-2 w-56 bg-white dark:bg-gray-800 rounded-lg shadow-dropdown border border-gray-200 dark:border-gray-700 z-10 hide-on-fullscreen-card hide-when-embedded">
										<a class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 add-to-mealplan-button"
											type="button"
											href="#"
											data-recipe-id="{{ $recipe->id }}">
											{{ $__t('Add to meal plan') }}
										</a>
										<a class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 recipe-delete"
											type="button"
											href="#"
											data-recipe-id="{{ $recipe->id }}"
											data-recipe-name="{{ $recipe->name }}">
											{{ $__t('Delete this item') }}
										</a>
										<a class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 recipe-copy"
											type="button"
											href="#"
											data-recipe-id="{{ $recipe->id }}">
											{{ $__t('Copy recipe') }}
										</a>
										<div class="border-t border-gray-200 dark:border-gray-700"></div>
										<a class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700"
											type="button"
											href="{{ $U('/recipe/' . $recipe->id . '/grocycode?download=true') }}">
											{!! str_replace('Grocycode', '<span class="ls-n1">Grocycode</span>', $__t('Download %s Grocycode', $__t('Recipe'))) !!}
										</a>
										@if(GROCY_FEATURE_FLAG_LABEL_PRINTER)
										<a class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 recipe-grocycode-label-print"
											data-recipe-id="{{ $recipe->id }}"
											type="button"
											href="#">
											{!! str_replace('Grocycode', '<span class="ls-n1">Grocycode</span>', $__t('Print %s Grocycode on label printer', $__t('Recipe'))) !!}
										</a>
										@endif
									</div>
								</div>
							</td>
							<td>
								{{ $recipe->name }}
							</td>
							<td>
								{{ $recipe->desired_servings }}
							</td>
							<td>
								{{ FindObjectInArrayByPropertyValue($recipesResolved, 'recipe_id', $recipe->id)->due_score }}
							</td>
							<td class="@if(!GROCY_FEATURE_FLAG_STOCK) hidden @endif">
								@if(FindObjectInArrayByPropertyValue($recipesResolved, 'recipe_id', $recipe->id)->need_fulfilled == 1)<i class="fa-solid fa-check text-success-600 dark:text-success-400"></i>@elseif(FindObjectInArrayByPropertyValue($recipesResolved, 'recipe_id', $recipe->id)->need_fulfilled_with_shopping_list == 1)<i class="fa-solid fa-exclamation text-warning-600 dark:text-warning-400"></i>@else<i class="fa-solid fa-times text-danger-600 dark:text-danger-400"></i>@endif
								<span class="timeago-contextual">@if(FindObjectInArrayByPropertyValue($recipesResolved, 'recipe_id', $recipe->id)->need_fulfilled == 1){{ $__t('Enough in stock') }}@elseif(FindObjectInArrayByPropertyValue($recipesResolved, 'recipe_id', $recipe->id)->need_fulfilled_with_shopping_list == 1){{ $__n(FindObjectInArrayByPropertyValue($recipesResolved, 'recipe_id', $recipe->id)->missing_products_count, 'Not enough in stock, %s ingredient missing but already on the shopping list', 'Not enough in stock, %s ingredients missing but already on the shopping list') }}@else{{ $__n(FindObjectInArrayByPropertyValue($recipesResolved, 'recipe_id', $recipe->id)->missing_products_count, 'Not enough in stock, %s ingredient missing', 'Not enough in stock, %s ingredients missing') }}@endif</span>
							</td>
							<td class="hidden">
								{{ FindObjectInArrayByPropertyValue($recipesResolved, 'recipe_id', $recipe->id)->missing_products_count }}
							</td>
							<td class="hidden">
								@if(FindObjectInArrayByPropertyValue($recipesResolved, 'recipe_id', $recipe->id)->need_fulfilled == 1) Xenoughinstock @elseif(FindObjectInArrayByPropertyValue($recipesResolved, 'recipe_id', $recipe->id)->need_fulfilled_with_shopping_list == 1) enoughinstockwithshoppinglist @else notenoughinstock @endif
							</td>
							<td class="hidden">
								{{ FindObjectInArrayByPropertyValue($recipesResolved, 'recipe_id', $recipe->id)->product_names_comma_separated }}
							</td>
							<td class="hidden">
								@if(FindObjectInArrayByPropertyValue($recipesResolved, 'recipe_id', $recipe->id)->need_fulfilled == 1) {{ $__t('Enough in stock') }} @elseif(FindObjectInArrayByPropertyValue($recipesResolved, 'recipe_id', $recipe->id)->need_fulfilled_with_shopping_list == 1) {{ $__t('Not enough in stock, but already on the shopping list') }} @else {{ $__t('Not enough in stock') }} @endif
							</td>

							@include('components.userfields_tbody', array(
							'userfields' => $userfields,
							'userfieldValues' => FindAllObjectsInArrayByPropertyValue($userfieldValues, 'object_id', $recipe->id)
							))

						</tr>
						@endforeach
					</tbody>
				</table>
				</div>

				<div x-show="activeTab === 'gallery'"
					id="gallery">
					<div class="card-columns mt-1">
					@foreach($recipes as $recipe)
					<div class="cursor-link recipe-gallery-item @if(FindObjectInArrayByPropertyValue($recipesResolved, 'recipe_id', $recipe->id)->need_fulfilled == 1) recipe-enoughinstock @elseif(FindObjectInArrayByPropertyValue($recipesResolved, 'recipe_id', $recipe->id)->need_fulfilled_with_shopping_list == 1) recipe-enoughinstockwithshoppinglist @else recipe-notenoughinstock @endif"
						data-recipe-id="{{ $recipe->id }}"
						href="#">
						<div id="RecipeGalleryCard-{{ $recipe->id }}"
							class="card recipe-card">
							@if(!empty($recipe->picture_file_name))
							<img src="{{ $U('/api/files/recipepictures/' . base64_encode($recipe->picture_file_name) . '?force_serve_as=picture&best_fit_width=400') }}"
								class="card-img-top"
								loading="lazy">
							@endif
							<div class="card-body text-center">
								<h5 class="text-lg font-semibold mb-1">{{ $recipe->name }}</h5>
								<span class="hidden card-title-search">
									{{ $recipe->name }}
									{{ FindObjectInArrayByPropertyValue($recipesResolved, 'recipe_id', $recipe->id)->product_names_comma_separated }}
								</span>
								<p class="mt-2">
									@if(FindObjectInArrayByPropertyValue($recipesResolved, 'recipe_id', $recipe->id)->need_fulfilled == 1)<i class="fa-solid fa-check text-success-600 dark:text-success-400"></i>@elseif(FindObjectInArrayByPropertyValue($recipesResolved, 'recipe_id', $recipe->id)->need_fulfilled_with_shopping_list == 1)<i class="fa-solid fa-exclamation text-warning-600 dark:text-warning-400"></i>@else<i class="fa-solid fa-times text-danger-600 dark:text-danger-400"></i>@endif
									<span class="timeago-contextual">@if(FindObjectInArrayByPropertyValue($recipesResolved, 'recipe_id', $recipe->id)->need_fulfilled == 1){{ $__t('Enough in stock') }}@elseif(FindObjectInArrayByPropertyValue($recipesResolved, 'recipe_id', $recipe->id)->need_fulfilled_with_shopping_list == 1){{ $__n(FindObjectInArrayByPropertyValue($recipesResolved, 'recipe_id', $recipe->id)->missing_products_count, 'Not enough in stock, %s ingredient missing but already on the shopping list', 'Not enough in stock, %s ingredients missing but already on the shopping list') }}@else{{ $__n(FindObjectInArrayByPropertyValue($recipesResolved, 'recipe_id', $recipe->id)->missing_products_count, 'Not enough in stock, %s ingredient missing', 'Not enough in stock, %s ingredients missing') }}@endif</span>
								</p>
								<p class="mt-2 flex justify-center gap-2">
									<a class="btn-danger text-xs px-2 py-1 hide-when-embedded hide-on-fullscreen-card recipe-delete"
										href="#"
										data-recipe-id="{{ $recipe->id }}"
										data-recipe-name="{{ $recipe->name }}"
										data-tooltip="{{ $__t('Delete this item') }}">
										<i class="fa-solid fa-trash"></i>
									</a>
									<a class="btn-primary text-xs px-2 py-1 hide-when-embedded hide-on-fullscreen-card recipe-edit-button"
										href="{{ $U('/recipe/') }}{{ $recipe->id }}"
										data-tooltip="{{ $__t('Edit this item') }}">
										<i class="fa-solid fa-edit"></i>
									</a>
								</p>
							</div>
						</div>
					</div>
					@endforeach
				</div>
			</div>
		</div>
		</div>
	</div>

	@if($selectedRecipe !== null && (boolval($userSettings['recipes_show_list_side_by_side']) || $embedded))
	@php
	$allRecipes = $selectedRecipeSubRecipes;
	array_unshift($allRecipes, $selectedRecipe);
	@endphp
	<div class="w-full md:w-1/2 px-2 print:block">
		<div id="selectedRecipeCard"
			class="card"
			x-data="{ activeRecipeTab: 0 }">
			@if(count($allRecipes) > 1)
			<div class="card-header mb-1 pt-0 print:hidden">
				<ul class="flex border-b border-gray-200 dark:border-gray-700">
					@foreach($allRecipes as $index=>$recipe)
					<li>
						<button @click="activeRecipeTab = {{ $index }}"
							:class="activeRecipeTab === {{ $index }} ? 'border-b-2 border-primary-500 text-primary-600 dark:text-primary-400' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300'"
							class="px-4 py-2 -mb-px font-medium">{{ $recipe->name }}</button>
					</li>
					@endforeach
				</ul>
			</div>
			@endif

			<div class="print:break-inside-avoid">
				@foreach($allRecipes as $index=>$recipe)
				<div x-show="activeRecipeTab === {{ $index }}"
					id="recipe-{{ $index + 1 }}">
					@if(!empty($recipe->picture_file_name))
					<img class="card-img-top"
						src="{{ $U('/api/files/recipepictures/' . base64_encode($recipe->picture_file_name) . '?force_serve_as=picture') }}"
						loading="lazy">
					@endif
					<div class="card-body">
						<div class="shadow-lg p-4 mb-5 bg-white dark:bg-gray-800 rounded-lg -mt-5 print:hidden @if(empty($recipe->picture_file_name)) hidden @endif">
							<div class="flex justify-between items-center">
								<h3 class="text-2xl font-bold mb-0">{{ $recipe->name }}</h3>
								<div class="flex flex-wrap justify-end flex-shrink gap-2">
									<a class="btn-ghost @if(!GROCY_FEATURE_FLAG_STOCK) hidden @endif recipe-consume"
										href="#"
										data-tooltip="{{ $__t('Consume all ingredients needed by this recipe') }}"
										data-recipe-id="{{ $recipe->id }}"
										data-recipe-name="{{ $recipe->name }}">
										<i class="fa-solid fa-utensils"></i>
									</a>
									<a class="btn-ghost @if(!GROCY_FEATURE_FLAG_SHOPPINGLIST) hidden @endif recipe-shopping-list @if(FindObjectInArrayByPropertyValue($recipesResolved, 'recipe_id', $recipe->id)->need_fulfilled_with_shopping_list == 1) opacity-50 pointer-events-none @endif"
										href="#"
										data-tooltip="{{ $__t('Put missing products on shopping list') }}"
										data-recipe-id="{{ $recipe->id }}"
										data-recipe-name="{{ $recipe->name }}">
										<i class="fa-solid fa-cart-plus"></i>
									</a>
									<a class="btn-ghost recipe-fullscreen hide-when-embedded"
										id="selectedRecipeToggleFullscreenButton"
										href="#"
										data-tooltip="{{ $__t('Expand to fullscreen') }}">
										<i class="fa-solid fa-expand-arrows-alt"></i>
									</a>
									<a class="btn-ghost recipe-print"
										href="#"
										data-tooltip="{{ $__t('Print') }}">
										<i class="fa-solid fa-print"></i>
									</a>
								</div>
							</div>
						</div>

						<div class="mb-4 @if(!empty($recipe->picture_file_name)) hidden @else flex @endif print:block justify-between items-center">
							<h1 class="text-3xl font-bold mb-0">{{ $recipe->name }}</h1>
							<div class="flex flex-wrap justify-end flex-shrink gap-2 print:hidden">
								<a class="btn-ghost recipe-consume"
									href="#"
									data-tooltip="{{ $__t('Consume all ingredients needed by this recipe') }}"
									data-recipe-id="{{ $recipe->id }}"
									data-recipe-name="{{ $recipe->name }}">
									<i class="fa-solid fa-utensils"></i>
								</a>
								<a class="btn-ghost recipe-shopping-list @if(FindObjectInArrayByPropertyValue($recipesResolved, 'recipe_id', $recipe->id)->need_fulfilled_with_shopping_list == 1) opacity-50 pointer-events-none @endif"
									href="#"
									data-tooltip="{{ $__t('Put missing products on shopping list') }}"
									data-recipe-id="{{ $recipe->id }}"
									data-recipe-name="{{ $recipe->name }}">
									<i class="fa-solid fa-cart-plus"></i>
								</a>
								<a class="btn-ghost recipe-fullscreen hide-when-embedded"
									href="#"
									data-tooltip="{{ $__t('Expand to fullscreen') }}">
									<i class="fa-solid fa-expand-arrows-alt"></i>
								</a>
								<a class="btn-ghost recipe-print PrintRecipe"
									href="#"
									data-tooltip="{{ $__t('Print') }}">
									<i class="fa-solid fa-print"></i>
								</a>
							</div>
						</div>

						@php
						$calories = FindObjectInArrayByPropertyValue($recipesResolved, 'recipe_id', $recipe->id)->calories;
						$costs = FindObjectInArrayByPropertyValue($recipesResolved, 'recipe_id', $recipe->id)->costs;
						@endphp

						<div class="flex flex-wrap gap-4 mb-4">
							@if(!empty($calories) && $calories > 0)
							<div class="flex-1">
								<label class="text-sm text-gray-600 dark:text-gray-400">{{ GROCY_ENERGY_UNIT }}&nbsp;
								<i class="fa-solid fa-question-circle text-gray-500 dark:text-gray-400 print:hidden"
									data-tooltip="{{ $__t('per serving') }}"></i>
								</label>
								<h3 class="locale-number locale-number-generic text-2xl font-bold pt-0">{{ $calories }}</h3>
							</div>
							@endif
							@if(GROCY_FEATURE_FLAG_STOCK_PRICE_TRACKING)
							<div class="flex-1">
								<label class="text-sm text-gray-600 dark:text-gray-400">{{ $__t('Costs') }}&nbsp;
									<i class="fa-solid fa-question-circle text-gray-500 dark:text-gray-400 print:hidden"
										data-tooltip="{{ $__t('Based on the prices of the default consume rule (Opened first, then first due first, then first in first out) for in stock ingredients and on the last price for missing ones') }}"></i>
								</label>
								<h3 class="text-2xl font-bold">
									<span class="locale-number locale-number-currency pt-0">{{ $costs }}</span>
									@if(FindObjectInArrayByPropertyValue($recipesResolved, 'recipe_id', $recipe->id)->prices_incomplete)
									<i class="text-sm fa-solid fa-exclamation text-danger-600 dark:text-danger-400"
										data-tooltip="{{ $__t('No price information is available for at least one ingredient') }}"></i>
									@endif
								</h3>
							</div>
							@endif

							@if($index == 0)
							<div class="flex-1 print:hidden">
								@include('components.numberpicker', array(
								'id' => 'servings-scale',
								'label' => 'Desired servings',
								'min' => $DEFAULT_MIN_AMOUNT,
								'decimals' => $userSettings['stock_decimal_places_amounts'],
								'value' => $recipe->desired_servings,
								'additionalAttributes' => 'data-recipe-id="' . $recipe->id . '"',
								'additionalCssClasses' => 'locale-number-input locale-number-quantity-amount'
								))
							</div>
							@endif
						</div>

						@php
						$recipePositionsFiltered = FindAllObjectsInArrayByPropertyValue($allRecipePositions[$recipe->id], 'recipe_id', $recipe->id);
						@endphp

						<div x-data="{ activeContentTab: 'ingredients' }">
							<ul class="flex border-b border-gray-200 dark:border-gray-700 mb-3 print:hidden hide-on-fullscreen-card">
								@if(count($recipePositionsFiltered) > 0)
								<li>
									<button @click="activeContentTab = 'ingredients'"
										:class="activeContentTab === 'ingredients' ? 'border-b-2 border-primary-500 text-primary-600 dark:text-primary-400' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300'"
										class="px-4 py-2 -mb-px font-medium">{{ $__t('Ingredients') }}</button>
								</li>
								@endif
								@if(!empty($recipe->description))
								<li>
									<button @click="activeContentTab = 'preparation'"
										:class="activeContentTab === 'preparation' ? 'border-b-2 border-primary-500 text-primary-600 dark:text-primary-400' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300'"
										class="px-4 py-2 -mb-px font-medium">{{ $__t('Preparation') }}</button>
								</li>
								@endif
							</ul>

							<div class="p-2 print:block recipe-content-container">
								@if(count($recipePositionsFiltered) > 0)
								<div x-show="activeContentTab === 'ingredients'"
									class="ingredients"
									id="ingredients-{{ $index }}">
								<div class="mb-2 hidden print:block recipe-headline">
									<h3 class="mb-0 text-xl font-bold">{{ $__t('Ingredients') }}</h3>
								</div>
								<ul class="space-y-1 mb-5">
									@php
									$lastIngredientGroup = 'undefined';
									$lastProductGroup = 'undefined';
									$hasIngredientGroups = false;
									$hasProductGroups = false;
									@endphp
									@foreach($recipePositionsFiltered as $selectedRecipePosition)
									@if($lastIngredientGroup != $selectedRecipePosition->ingredient_group && !empty($selectedRecipePosition->ingredient_group))
									@php $hasIngredientGroups = true; @endphp
									<h5 class="mb-2 mt-2 ml-1"><strong>{{ $selectedRecipePosition->ingredient_group }}</strong></h5>
									@endif
									@if(boolval($userSettings['recipe_ingredients_group_by_product_group']) && $lastProductGroup != $selectedRecipePosition->product_group && !empty($selectedRecipePosition->product_group))
									@php $hasProductGroups = true; @endphp
									<h6 class="mb-2 mt-2 @if($hasIngredientGroups) ml-3 @else ml-1 @endif"><strong>{{ $selectedRecipePosition->product_group }}</strong></h6>
									@endif
									<li class="py-2 border-b border-gray-200 dark:border-gray-700 @if($hasIngredientGroups && $hasProductGroups) ml-4 @elseif($hasIngredientGroups || $hasProductGroups) ml-2 @else ml-0 @endif">
										@if($selectedRecipePosition->product_active == 0)
										<div class="text-sm text-gray-500 dark:text-gray-400 italic">{{ $__t('Disabled') }}</div>
										@endif
										@if($userSettings['recipes_show_ingredient_checkbox'])
										<a class="btn-ghost text-sm px-2 py-1 ingredient-done-button"
											href="#"
											data-tooltip="{{ $__t('Mark this item as done') }}">
											<i class="fa-solid fa-check-circle text-primary-600 dark:text-primary-400"></i>
										</a>
										@endif
										@php
										$product = FindObjectInArrayByPropertyValue($products, 'id', $selectedRecipePosition->product_id);
										$productQuConversions = FindAllObjectsInArrayByPropertyValue($quantityUnitConversionsResolved, 'product_id', $product->id);
										$productQuConversions = FindAllObjectsInArrayByPropertyValue($productQuConversions, 'from_qu_id', $product->qu_id_stock);
										$productQuConversion = FindObjectInArrayByPropertyValue($productQuConversions, 'to_qu_id', $selectedRecipePosition->qu_id);
										if ($productQuConversion && $selectedRecipePosition->only_check_single_unit_in_stock == 0)
										{
										$selectedRecipePosition->recipe_amount = $selectedRecipePosition->recipe_amount * $productQuConversion->factor;
										}
										@endphp
										<span class="productcard-trigger cursor-link @if($selectedRecipePosition->due_score == 20) text-danger @elseif($selectedRecipePosition->due_score == 10) text-secondary @elseif($selectedRecipePosition->due_score == 1) text-warning @endif"
											data-product-id="{{ $selectedRecipePosition->product_id }}">
											@if(!empty($selectedRecipePosition->recipe_variable_amount))
											{{ $selectedRecipePosition->recipe_variable_amount }}
											@else
											<span class="locale-number locale-number-quantity-amount">@if($selectedRecipePosition->recipe_amount == round($selectedRecipePosition->recipe_amount, 2)){{ round($selectedRecipePosition->recipe_amount, 2) }}@else{{ $selectedRecipePosition->recipe_amount }}@endif</span>
											{{ $__n($selectedRecipePosition->recipe_amount, FindObjectInArrayByPropertyValue($quantityUnits, 'id', $selectedRecipePosition->qu_id)->name, FindObjectInArrayByPropertyValue($quantityUnits, 'id', $selectedRecipePosition->qu_id)->name_plural) }}
											@endif
											{{ FindObjectInArrayByPropertyValue($products, 'id', $selectedRecipePosition->product_id)->name }}
										</span>
										@if(GROCY_FEATURE_FLAG_STOCK)
										<span class="
												d-print-none">
											@if(FindObjectInArrayByPropertyValue($recipePositionsResolved, 'recipe_pos_id', $selectedRecipePosition->id)->need_fulfilled == 1)<i class="fa-solid fa-check text-success"></i>@elseif(FindObjectInArrayByPropertyValue($recipePositionsResolved, 'recipe_pos_id', $selectedRecipePosition->id)->need_fulfilled_with_shopping_list == 1)<i class="fa-solid fa-exclamation text-warning"></i>@else<i class="fa-solid fa-times text-danger"></i>@endif
											<span class="timeago-contextual">@if(FindObjectInArrayByPropertyValue($recipePositionsResolved, 'recipe_pos_id', $selectedRecipePosition->id)->need_fulfilled == 1) {{ $__t('Enough in stock') }} (<span class="locale-number locale-number-quantity-amount">{{ $selectedRecipePosition->stock_amount }}</span> {{ $__n($selectedRecipePosition->stock_amount, FindObjectInArrayByPropertyValue($quantityUnits, 'id', $product->qu_id_stock)->name, FindObjectInArrayByPropertyValue($quantityUnits, 'id', $product->qu_id_stock)->name_plural) }}) @else {{ $__t('Not enough in stock, %1$s missing, %2$s already on shopping list', round($selectedRecipePosition->missing_amount, 2), round($selectedRecipePosition->amount_on_shopping_list, 2)) }} @endif</span>
										</span>
										@endif
										@if($selectedRecipePosition->product_id != $selectedRecipePosition->product_id_effective)
										<br class="d-print-none">
										<span class="productcard-trigger cursor-link text-muted d-print-none"
											data-product-id="{{ $selectedRecipePosition->product_id_effective }}"
											data-toggle="tooltip"
											data-trigger="hover click"
											title="{{ $__t('The parent product %1$s is currently not in stock, %2$s is the current next sub product based on the default consume rule (Opened first, then first due first, then first in first out)', FindObjectInArrayByPropertyValue($products, 'id', $selectedRecipePosition->product_id)->name, FindObjectInArrayByPropertyValue($products, 'id', $selectedRecipePosition->product_id_effective)->name) }}">
											<i class="fa-solid fa-exchange-alt"></i> {{ FindObjectInArrayByPropertyValue($products, 'id', $selectedRecipePosition->product_id_effective)->name }}
										</span>
										@endif
										@if(GROCY_FEATURE_FLAG_STOCK_PRICE_TRACKING) <span class="float-right font-italic ml-2 locale-number locale-number-currency">{{ $selectedRecipePosition->costs }}</span> @endif
										<span class="float-right font-italic"><span class="locale-number locale-number-generic">{{ $selectedRecipePosition->calories }}</span> {{ $__t('Calories') }}</span>
										@if(!empty($selectedRecipePosition->recipe_variable_amount))
										<div class="small text-muted font-italic">{{ $__t('Variable amount') }}</div>
										@endif

										@if(!empty($selectedRecipePosition->note))
										<div class="text-muted">{!! nl2br($selectedRecipePosition->note ?? '') !!}</div>
										@endif
									</li>
									@php $lastProductGroup = $selectedRecipePosition->product_group; @endphp
									@php $lastIngredientGroup = $selectedRecipePosition->ingredient_group; @endphp
									@endforeach
								</ul>
							</div>
							@endif
							<div x-show="activeContentTab === 'preparation'"
								class="preparation"
								id="prep-{{ $index }}">
								<div class="mb-2 hidden print:block recipe-headline">
									<h3 class="mb-0 text-xl font-bold">{{ $__t('Preparation') }}</h3>
								</div>
								@if(!empty($recipe->description))
								{!! $recipe->description !!}
								@endif
							</div>
						</div>
						</div>
					</div>
				</div>

				<div id="missing-recipe-pos-list"
					class="space-y-2 hidden mt-3">
					@foreach($recipePositionsResolved as $recipePos)
					@if(in_array($recipePos->recipe_id, $includedRecipeIdsAbsolute) && $recipePos->missing_amount > 0)
					<a href="#"
						class="flex items-center gap-2 px-4 py-2 bg-primary-50 dark:bg-primary-900 hover:bg-primary-100 dark:hover:bg-primary-800 rounded border border-primary-200 dark:border-primary-700 missing-recipe-pos-select-button">
						<input class="checkbox missing-recipe-pos-product-checkbox"
							type="checkbox"
							data-product-id="{{ $recipePos->product_id }}"
							checked>
						{{ FindObjectInArrayByPropertyValue($products, 'id', $recipePos->product_id)->name }}
					</a>
					@endif
					@endforeach
				</div>
				@endforeach
			</div>
		</div>
	</div>
	@endif
</div>

<div x-data="{ open: false }"
	x-show="open"
	@add-to-mealplan-modal-open.window="open = true"
	id="add-to-mealplan-modal"
	class="fixed inset-0 z-50 overflow-y-auto"
	x-cloak>
	<div class="flex items-center justify-center min-h-screen px-4">
		<div class="fixed inset-0 bg-gray-500 dark:bg-gray-900 bg-opacity-75 dark:bg-opacity-75 transition-opacity"
			@click="open = false"></div>
		<div class="card relative max-w-lg w-full">
			<div class="card-header flex justify-between items-center">
				<h4 class="text-lg font-semibold">
					<span>{{ $__t('Add meal plan entry') }}</span>
				</h4>
				<span class="text-gray-500 dark:text-gray-400">{{ $__t('Recipe') }}</span>
			</div>
			<div class="card-body">
				<form id="add-to-mealplan-form"
					novalidate>

					@include('components.datetimepicker', array(
					'id' => 'day',
					'label' => 'Day',
					'format' => 'YYYY-MM-DD',
					'initWithNow' => false,
					'limitEndToNow' => false,
					'limitStartToNow' => false,
					'isRequired' => true,
					'additionalCssClasses' => 'date-only-datetimepicker',
					'invalidFeedback' => $__t('A date is required')
					))

					@include('components.recipepicker', array(
					'recipes' => $recipes,
					'isRequired' => true,
					'nextInputSelector' => '#recipe_servings'
					))

					@include('components.numberpicker', array(
					'id' => 'recipe_servings',
					'label' => 'Servings',
					'min' => $DEFAULT_MIN_AMOUNT,
					'decimals' => $userSettings['stock_decimal_places_amounts'],
					'value' => '1',
					'additionalCssClasses' => 'locale-number-input locale-number-quantity-amount'
					))

					<div class="mb-4">
						<label for="section_id" class="block text-sm font-medium mb-1">{{ $__t('Section') }}</label>
						<select class="select w-full"
							id="section_id"
							name="section_id"
							required>
							@foreach($mealplanSections as $mealplanSection)
							<option value="{{ $mealplanSection->id }}">{{ $mealplanSection->name }}</option>
							@endforeach
						</select>
					</div>

					<input type="hidden"
						name="type"
						value="recipe">
				</form>
			</div>
			<div class="flex justify-end gap-2 px-4 pb-4 border-t border-gray-200 dark:border-gray-700 pt-3">
				<button type="button"
					class="btn-secondary"
					@click="open = false">{{ $__t('Cancel') }}</button>
				<button id="save-add-to-mealplan-button"
					class="btn-primary">{{ $__t('Save') }}</button>
			</div>
		</div>
	</div>
</div>

@include('components.productcard', [
'asModal' => true
])
@stop
