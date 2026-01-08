{{-- Sidebar Navigation Partial --}}
{{-- Uses Alpine.js for collapse/expand functionality --}}

<aside class="fixed inset-y-0 left-0 z-50 flex flex-col bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 transition-all duration-200"
	:class="{
		'w-64': sidebarOpen,
		'w-16': !sidebarOpen,
		'translate-x-0': sidebarMobile,
		'-translate-x-full lg:translate-x-0': !sidebarMobile
	}">

	{{-- Logo area --}}
	<div class="flex items-center h-16 px-4 border-b border-gray-200 dark:border-gray-700 shrink-0">
		<a href="{{ $U('/') }}" class="flex items-center gap-3 overflow-hidden">
			<img src="{{ $U('/img/logo.svg?v=', true) }}{{ $version }}"
				class="h-8 w-auto shrink-0"
				:class="{ 'hidden': !sidebarOpen }"
				alt="Grocy">
			<img src="{{ $U('/img/icon-32.png?v=', true) }}{{ $version }}"
				class="h-8 w-8 shrink-0"
				:class="{ 'hidden': sidebarOpen }"
				alt="Grocy">
		</a>
	</div>

	{{-- Navigation --}}
	<nav class="flex-1 overflow-y-auto py-4 px-2" x-data>
		<ul class="space-y-1">
			{{-- Stock Overview --}}
			@if(GROCY_FEATURE_FLAG_STOCK)
			<li>
				<a href="{{ $U('/stockoverview') }}"
					class="nav-link @if($viewName == 'stockoverview') active @endif"
					title="{{ $__t('Stock overview') }}">
					<i class="fa-solid fa-fw fa-box"></i>
					<span class="nav-link-text" x-show="sidebarOpen" x-cloak>{{ $__t('Stock overview') }}</span>
				</a>
			</li>
			@endif

			{{-- Shopping List --}}
			@if(GROCY_FEATURE_FLAG_SHOPPINGLIST)
			<li>
				<a href="{{ $U('/shoppinglist') }}"
					class="nav-link @if($viewName == 'shoppinglist') active @endif"
					title="{{ $__t('Shopping list') }}">
					<i class="fa-solid fa-fw fa-shopping-cart"></i>
					<span class="nav-link-text" x-show="sidebarOpen" x-cloak>{{ $__t('Shopping list') }}</span>
				</a>
			</li>
			@endif

			{{-- Recipes Section --}}
			@if(GROCY_FEATURE_FLAG_RECIPES)
			<li class="nav-divider" x-show="sidebarOpen" x-cloak></li>

			<li class="permission-RECIPES">
				<a href="{{ $U('/recipes') }}"
					class="nav-link @if($viewName == 'recipes') active @endif"
					title="{{ $__t('Recipes') }}">
					<i class="fa-solid fa-fw fa-pizza-slice"></i>
					<span class="nav-link-text" x-show="sidebarOpen" x-cloak>{{ $__t('Recipes') }}</span>
				</a>
			</li>

			@if(GROCY_FEATURE_FLAG_RECIPES_MEALPLAN)
			<li class="permission-RECIPES_MEALPLAN">
				<a href="{{ $U('/mealplan') }}"
					id="meal-plan-nav-link"
					class="nav-link @if($viewName == 'mealplan') active @endif"
					title="{{ $__t('Meal plan') }}">
					<i class="fa-solid fa-fw fa-paper-plane"></i>
					<span class="nav-link-text" x-show="sidebarOpen" x-cloak>{{ $__t('Meal plan') }}</span>
				</a>
			</li>
			@endif
			@endif

			{{-- Chores & Tasks Section --}}
			@if(GROCY_FEATURE_FLAG_CHORES)
			<li class="nav-divider" x-show="sidebarOpen" x-cloak></li>

			<li>
				<a href="{{ $U('/choresoverview') }}"
					class="nav-link @if($viewName == 'choresoverview') active @endif"
					title="{{ $__t('Chores overview') }}">
					<i class="fa-solid fa-fw fa-home"></i>
					<span class="nav-link-text" x-show="sidebarOpen" x-cloak>{{ $__t('Chores overview') }}</span>
				</a>
			</li>
			@endif

			@if(GROCY_FEATURE_FLAG_TASKS)
			<li>
				<a href="{{ $U('/tasks') }}"
					class="nav-link @if($viewName == 'tasks') active @endif"
					title="{{ $__t('Tasks') }}">
					<i class="fa-solid fa-fw fa-tasks"></i>
					<span class="nav-link-text" x-show="sidebarOpen" x-cloak>{{ $__t('Tasks') }}</span>
				</a>
			</li>
			@endif

			@if(GROCY_FEATURE_FLAG_BATTERIES)
			<li>
				<a href="{{ $U('/batteriesoverview') }}"
					class="nav-link @if($viewName == 'batteriesoverview') active @endif"
					title="{{ $__t('Batteries overview') }}">
					<i class="fa-solid fa-fw fa-battery-half"></i>
					<span class="nav-link-text" x-show="sidebarOpen" x-cloak>{{ $__t('Batteries overview') }}</span>
				</a>
			</li>
			@endif

			{{-- Equipment --}}
			@if(GROCY_FEATURE_FLAG_EQUIPMENT)
			<li class="permission-EQUIPMENT">
				<a href="{{ $U('/equipment') }}"
					class="nav-link @if($viewName == 'equipment') active @endif"
					title="{{ $__t('Equipment') }}">
					<i class="fa-solid fa-fw fa-toolbox"></i>
					<span class="nav-link-text" x-show="sidebarOpen" x-cloak>{{ $__t('Equipment') }}</span>
				</a>
			</li>
			@endif

			{{-- Calendar --}}
			@if(GROCY_FEATURE_FLAG_CALENDAR)
			<li class="nav-divider" x-show="sidebarOpen" x-cloak></li>

			<li class="permission-CALENDAR">
				<a href="{{ $U('/calendar') }}"
					class="nav-link @if($viewName == 'calendar') active @endif"
					title="{{ $__t('Calendar') }}">
					<i class="fa-solid fa-fw fa-calendar-days"></i>
					<span class="nav-link-text" x-show="sidebarOpen" x-cloak>{{ $__t('Calendar') }}</span>
				</a>
			</li>
			@endif

			{{-- Stock Actions Section --}}
			@if(GROCY_FEATURE_FLAG_STOCK)
			<li class="nav-divider" x-show="sidebarOpen" x-cloak></li>

			<li class="permission-STOCK_PURCHASE">
				<a href="{{ $U('/purchase') }}"
					class="nav-link @if($viewName == 'purchase') active @endif"
					title="{{ $__t('Purchase') }}">
					<i class="fa-solid fa-fw fa-cart-plus"></i>
					<span class="nav-link-text" x-show="sidebarOpen" x-cloak>{{ $__t('Purchase') }}</span>
				</a>
			</li>

			<li class="permission-STOCK_CONSUME">
				<a href="{{ $U('/consume') }}"
					class="nav-link @if($viewName == 'consume') active @endif"
					title="{{ $__t('Consume') }}">
					<i class="fa-solid fa-fw fa-utensils"></i>
					<span class="nav-link-text" x-show="sidebarOpen" x-cloak>{{ $__t('Consume') }}</span>
				</a>
			</li>

			@if(GROCY_FEATURE_FLAG_STOCK_LOCATION_TRACKING)
			<li class="permission-STOCK_TRANSFER">
				<a href="{{ $U('/transfer') }}"
					class="nav-link @if($viewName == 'transfer') active @endif"
					title="{{ $__t('Transfer') }}">
					<i class="fa-solid fa-fw fa-exchange-alt"></i>
					<span class="nav-link-text" x-show="sidebarOpen" x-cloak>{{ $__t('Transfer') }}</span>
				</a>
			</li>
			@endif

			<li class="permission-STOCK_INVENTORY">
				<a href="{{ $U('/inventory') }}"
					class="nav-link @if($viewName == 'inventory') active @endif"
					title="{{ $__t('Inventory') }}">
					<i class="fa-solid fa-fw fa-list"></i>
					<span class="nav-link-text" x-show="sidebarOpen" x-cloak>{{ $__t('Inventory') }}</span>
				</a>
			</li>
			@endif

			{{-- Chore Tracking --}}
			@if(GROCY_FEATURE_FLAG_CHORES)
			<li class="permission-CHORE_TRACK_EXECUTION">
				<a href="{{ $U('/choretracking') }}"
					class="nav-link @if($viewName == 'choretracking') active @endif"
					title="{{ $__t('Chore tracking') }}">
					<i class="fa-solid fa-fw fa-play"></i>
					<span class="nav-link-text" x-show="sidebarOpen" x-cloak>{{ $__t('Chore tracking') }}</span>
				</a>
			</li>
			@endif

			{{-- Battery Tracking --}}
			@if(GROCY_FEATURE_FLAG_BATTERIES)
			<li class="permission-BATTERIES_TRACK_CHARGE_CYCLE">
				<a href="{{ $U('/batterytracking') }}"
					class="nav-link @if($viewName == 'batterytracking') active @endif"
					title="{{ $__t('Battery tracking') }}">
					<i class="fa-solid fa-fw fa-car-battery"></i>
					<span class="nav-link-text" x-show="sidebarOpen" x-cloak>{{ $__t('Battery tracking') }}</span>
				</a>
			</li>
			@endif

			{{-- User Entities --}}
			@php $firstUserentity = true; @endphp
			@foreach($userentitiesForSidebar as $userentity)
			@if($firstUserentity)
			<li class="nav-divider" x-show="sidebarOpen" x-cloak></li>
			@php $firstUserentity = false; @endphp
			@endif
			<li>
				<a href="{{ $U('/userobjects/' . $userentity->name) }}"
					class="nav-link @if($viewName == 'userobjects' && $__env->yieldContent('title') == $userentity->caption) active @endif"
					title="{{ $userentity->caption }}">
					<i class="fa-fw {{ $userentity->icon_css_class }}"></i>
					<span class="nav-link-text" x-show="sidebarOpen" x-cloak>{{ $userentity->caption }}</span>
				</a>
			</li>
			@endforeach

			{{-- Master Data Section --}}
			@php
			$masterDataViews = [
				'products', 'locations', 'shoppinglocations', 'quantityunits',
				'productgroups', 'chores', 'batteries', 'taskcategories',
				'userfields', 'userentities'
			];
			@endphp

			<li class="nav-divider" x-show="sidebarOpen" x-cloak></li>

			{{-- Master Data Collapsible Menu --}}
			<li x-data="{ open: masterDataOpen }">
				<button @click="open = !open; masterDataOpen = open"
					class="nav-link w-full justify-between @if(in_array($viewName, $masterDataViews)) active @endif"
					title="{{ $__t('Manage master data') }}">
					<span class="flex items-center gap-3">
						<i class="fa-solid fa-fw fa-table"></i>
						<span class="nav-link-text" x-show="sidebarOpen" x-cloak>{{ $__t('Manage master data') }}</span>
					</span>
					<i class="fa-solid fa-chevron-down text-xs transition-transform duration-200"
						:class="{ 'rotate-180': open }"
						x-show="sidebarOpen"
						x-cloak></i>
				</button>

				<ul x-show="open && sidebarOpen"
					x-collapse
					class="mt-1 ml-4 space-y-1 border-l border-gray-200 dark:border-gray-600 pl-3">
					<li>
						<a href="{{ $U('/products') }}"
							class="nav-link-sub @if($viewName == 'products') active @endif">
							{{ $__t('Products') }}
						</a>
					</li>

					@if(GROCY_FEATURE_FLAG_STOCK && GROCY_FEATURE_FLAG_STOCK_LOCATION_TRACKING)
					<li>
						<a href="{{ $U('/locations') }}"
							class="nav-link-sub @if($viewName == 'locations') active @endif">
							{{ $__t('Locations') }}
						</a>
					</li>
					@endif

					@if(GROCY_FEATURE_FLAG_STOCK && GROCY_FEATURE_FLAG_STOCK_PRICE_TRACKING)
					<li>
						<a href="{{ $U('/shoppinglocations') }}"
							class="nav-link-sub @if($viewName == 'shoppinglocations') active @endif">
							{{ $__t('Stores') }}
						</a>
					</li>
					@endif

					<li>
						<a href="{{ $U('/quantityunits') }}"
							class="nav-link-sub @if($viewName == 'quantityunits') active @endif">
							{{ $__t('Quantity units') }}
						</a>
					</li>

					<li>
						<a href="{{ $U('/productgroups') }}"
							class="nav-link-sub @if($viewName == 'productgroups') active @endif">
							{{ $__t('Product groups') }}
						</a>
					</li>

					@if(GROCY_FEATURE_FLAG_CHORES)
					<li>
						<a href="{{ $U('/chores') }}"
							class="nav-link-sub @if($viewName == 'chores') active @endif">
							{{ $__t('Chores') }}
						</a>
					</li>
					@endif

					@if(GROCY_FEATURE_FLAG_BATTERIES)
					<li>
						<a href="{{ $U('/batteries') }}"
							class="nav-link-sub @if($viewName == 'batteries') active @endif">
							{{ $__t('Batteries') }}
						</a>
					</li>
					@endif

					@if(GROCY_FEATURE_FLAG_TASKS)
					<li>
						<a href="{{ $U('/taskcategories') }}"
							class="nav-link-sub @if($viewName == 'taskcategories') active @endif">
							{{ $__t('Task categories') }}
						</a>
					</li>
					@endif

					<li>
						<a href="{{ $U('/userfields') }}"
							class="nav-link-sub @if($viewName == 'userfields') active @endif">
							{{ $__t('Userfields') }}
						</a>
					</li>

					<li>
						<a href="{{ $U('/userentities') }}"
							class="nav-link-sub @if($viewName == 'userentities') active @endif">
							{{ $__t('Userentities') }}
						</a>
					</li>
				</ul>
			</li>
		</ul>
	</nav>

	{{-- Sidebar collapse toggle (desktop) --}}
	<div class="hidden lg:flex items-center justify-center h-12 border-t border-gray-200 dark:border-gray-700 shrink-0">
		<button @click="sidebarOpen = !sidebarOpen"
			class="p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-md transition-colors"
			title="{{ $__t('Toggle sidebar') }}">
			<i class="fa-solid fa-angle-left transition-transform duration-200"
				:class="{ 'rotate-180': !sidebarOpen }"></i>
		</button>
	</div>
</aside>

