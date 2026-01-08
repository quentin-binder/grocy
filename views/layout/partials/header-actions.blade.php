{{-- Header Actions Partial --}}
{{-- Contains mobile menu button, page title, clock, and action dropdowns --}}

<header class="sticky top-0 z-30 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
	<div class="flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">
		{{-- Left side: Mobile menu button + Page title --}}
		<div class="flex items-center gap-4">
			{{-- Mobile menu button --}}
			<button @click="sidebarMobile = !sidebarMobile"
				class="lg:hidden p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-md transition-colors">
				<i class="fa-solid fa-bars text-lg"></i>
			</button>

			{{-- Page title --}}
			<h1 class="text-lg font-semibold text-gray-900 dark:text-gray-100 truncate">
				@yield('title')
			</h1>

			{{-- Clock --}}
			<span id="clock-container"
				class="text-gray-500 dark:text-gray-400 text-sm italic hidden">
				<i class="fa-solid fa-clock mr-1"></i>
				<span id="clock-small" class="inline sm:hidden"></span>
				<span id="clock-big" class="hidden sm:inline"></span>
			</span>
		</div>

		{{-- Right side: Actions --}}
		<div class="flex items-center gap-2">
			{{-- User menu (if authenticated and not embedded) --}}
			@if(GROCY_AUTHENTICATED && !GROCY_IS_EMBEDDED_INSTALL && !GROCY_DISABLE_AUTH)
			<div x-data="{ open: false }" class="relative">
				<button @click="open = !open"
					@click.away="open = false"
					class="flex items-center gap-2 px-3 py-1.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-md transition-colors">
					@if(empty(GROCY_USER_PICTURE_FILE_NAME))
					<i class="fa-solid fa-user"></i>
					@else
					<img class="h-6 w-6 rounded-full object-cover"
						src="{{ $U('/files/userpictures/' . base64_encode(GROCY_USER_PICTURE_FILE_NAME) . '_' . base64_encode(GROCY_USER_PICTURE_FILE_NAME) . '?force_serve_as=picture&best_fit_width=32&best_fit_height=32') }}"
						loading="lazy"
						alt="">
					@endif
					<span class="hidden sm:inline">{{ GROCY_USER_USERNAME }}</span>
					<i class="fa-solid fa-chevron-down text-xs"></i>
				</button>

				<div x-show="open"
					x-transition:enter="transition ease-out duration-100"
					x-transition:enter-start="opacity-0 scale-95"
					x-transition:enter-end="opacity-100 scale-100"
					x-transition:leave="transition ease-in duration-75"
					x-transition:leave-start="opacity-100 scale-100"
					x-transition:leave-end="opacity-0 scale-95"
					class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow-lg py-1 z-50"
					x-cloak>
					<a href="{{ $U('/logout') }}"
						class="dropdown-item logout-button">
						<i class="fa-solid fa-fw fa-sign-out-alt mr-2"></i>{{ $__t('Logout') }}
					</a>
					<div class="border-t border-gray-200 dark:border-gray-700 my-1"></div>
					@if(!defined('GROCY_EXTERNALLY_MANAGED_AUTHENTICATION'))
					<a href="{{ $U('/user/' . GROCY_USER_ID . '?changepw=true') }}"
						class="dropdown-item">
						<i class="fa-solid fa-fw fa-key mr-2"></i>{{ $__t('Change password') }}
					</a>
					@else
					<a href="{{ $U('/user/' . GROCY_USER_ID) }}"
						class="dropdown-item">
						<i class="fa-solid fa-fw fa-key mr-2"></i>{{ $__t('Edit user') }}
					</a>
					@endif
				</div>
			</div>
			@endif

			{{-- View settings dropdown --}}
			@if(GROCY_AUTHENTICATED)
			<div x-data="{ open: false }" class="relative">
				<button @click="open = !open"
					@click.away="open = false"
					class="p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-md transition-colors"
					title="{{ $__t('View settings') }}">
					<i class="fa-solid fa-sliders-h"></i>
				</button>

				<div x-show="open"
					x-transition:enter="transition ease-out duration-100"
					x-transition:enter-start="opacity-0 scale-95"
					x-transition:enter-end="opacity-100 scale-100"
					x-transition:leave="transition ease-in duration-75"
					x-transition:leave-start="opacity-100 scale-100"
					x-transition:leave-end="opacity-0 scale-95"
					class="absolute right-0 mt-2 w-72 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow-lg py-2 z-50"
					x-cloak>

					{{-- Auto reload --}}
					<div class="px-3 py-2">
						<label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300 cursor-pointer">
							<input type="checkbox"
								class="checkbox user-setting-control"
								id="auto-reload-enabled"
								data-setting-key="auto_reload_on_db_change">
							<span>{{ $__t('Auto reload on external changes') }}</span>
						</label>
					</div>

					{{-- Show clock --}}
					<div class="px-3 py-2">
						<label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300 cursor-pointer">
							<input type="checkbox"
								class="checkbox user-setting-control"
								id="show-clock-in-header"
								data-setting-key="show_clock_in_header">
							<span>{{ $__t('Show clock in header') }}</span>
						</label>
					</div>

					<div class="border-t border-gray-200 dark:border-gray-700 my-2"></div>

					{{-- Night mode options --}}
					<div class="px-3 py-2">
						<span class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ $__t('Night mode') }}</span>
						<div class="space-y-2">
							<label class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400 cursor-pointer">
								<input type="radio"
									class="radio user-setting-control"
									name="night-mode"
									id="night-mode-on"
									value="on"
									data-setting-key="night_mode">
								<span>{{ $__t('On') }}</span>
							</label>
							<label class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400 cursor-pointer">
								<input type="radio"
									class="radio user-setting-control"
									name="night-mode"
									id="night-mode-follow-system"
									value="follow-system"
									data-setting-key="night_mode">
								<span>{{ $__t('Use system setting') }}</span>
							</label>
							<label class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400 cursor-pointer">
								<input type="radio"
									class="radio user-setting-control"
									name="night-mode"
									id="night-mode-off"
									value="off"
									data-setting-key="night_mode">
								<span>{{ $__t('Off') }}</span>
							</label>
						</div>
					</div>

					{{-- Auto night mode time range --}}
					<div class="px-3 py-2">
						<label class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400 cursor-pointer">
							<input type="checkbox"
								class="checkbox user-setting-control"
								id="auto-night-mode-enabled"
								data-setting-key="auto_night_mode_enabled">
							<span>{{ $__t('Auto enable in time range') }}</span>
						</label>
						<div class="flex gap-2 mt-2">
							<input type="text"
								class="input text-xs"
								readonly
								id="auto-night-mode-time-range-from"
								placeholder="{{ $__t('From') }} (HH:mm)"
								data-setting-key="auto_night_mode_time_range_from">
							<input type="text"
								class="input text-xs"
								readonly
								id="auto-night-mode-time-range-to"
								placeholder="{{ $__t('To') }} (HH:mm)"
								data-setting-key="auto_night_mode_time_range_to">
						</div>
						<label class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400 cursor-pointer mt-2">
							<input type="checkbox"
								class="checkbox user-setting-control"
								id="auto-night-mode-time-range-goes-over-midgnight"
								data-setting-key="auto_night_mode_time_range_goes_over_midnight">
							<span>{{ $__t('Time range goes over midnight') }}</span>
						</label>
					</div>

					<div class="border-t border-gray-200 dark:border-gray-700 my-2"></div>

					{{-- Screen options --}}
					<div class="px-3 py-2">
						<label class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400 cursor-pointer">
							<input type="checkbox"
								class="checkbox user-setting-control"
								id="keep_screen_on"
								data-setting-key="keep_screen_on">
							<span>{{ $__t('Keep screen on') }}</span>
						</label>
					</div>

					<div class="px-3 py-2">
						<label class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400 cursor-pointer">
							<input type="checkbox"
								class="checkbox user-setting-control"
								id="keep_screen_on_when_fullscreen_card"
								data-setting-key="keep_screen_on_when_fullscreen_card">
							<span>{{ $__t('Keep screen on while displaying a "fullscreen-card"') }}</span>
						</label>
					</div>
				</div>
			</div>
			@endif

			{{-- Settings dropdown --}}
			<div x-data="{ open: false }" class="relative">
				<button @click="open = !open"
					@click.away="open = false"
					class="p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-md transition-colors"
					title="{{ $__t('Settings') }}">
					<i class="fa-solid fa-wrench"></i>
				</button>

				<div x-show="open"
					x-transition:enter="transition ease-out duration-100"
					x-transition:enter-start="opacity-0 scale-95"
					x-transition:enter-end="opacity-100 scale-100"
					x-transition:leave="transition ease-in duration-75"
					x-transition:leave-start="opacity-100 scale-100"
					x-transition:leave-end="opacity-0 scale-95"
					class="absolute right-0 mt-2 w-56 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow-lg py-1 z-50"
					x-cloak>

					{{-- Feature settings --}}
					<a href="{{ $U('/stocksettings') }}" class="dropdown-item">
						<i class="fa-solid fa-fw fa-box mr-2"></i>{{ $__t('Stock settings') }}
					</a>

					@if(GROCY_FEATURE_FLAG_SHOPPINGLIST)
					<a href="{{ $U('/shoppinglistsettings') }}" class="dropdown-item permission-SHOPPINGLIST">
						<i class="fa-solid fa-fw fa-shopping-cart mr-2"></i>{{ $__t('Shopping list settings') }}
					</a>
					@endif

					@if(GROCY_FEATURE_FLAG_RECIPES)
					<a href="{{ $U('/recipessettings') }}" class="dropdown-item permission-RECIPES">
						<i class="fa-solid fa-fw fa-pizza-slice mr-2"></i>{{ $__t('Recipes settings') }}
					</a>
					@endif

					@if(GROCY_FEATURE_FLAG_CHORES)
					<a href="{{ $U('/choressettings') }}" class="dropdown-item permission-CHORES">
						<i class="fa-solid fa-fw fa-home mr-2"></i>{{ $__t('Chores settings') }}
					</a>
					@endif

					@if(GROCY_FEATURE_FLAG_TASKS)
					<a href="{{ $U('/taskssettings') }}" class="dropdown-item permission-TASKS">
						<i class="fa-solid fa-fw fa-tasks mr-2"></i>{{ $__t('Tasks settings') }}
					</a>
					@endif

					@if(GROCY_FEATURE_FLAG_BATTERIES)
					<a href="{{ $U('/batteriessettings') }}" class="dropdown-item permission-BATTERIES">
						<i class="fa-solid fa-fw fa-battery-half mr-2"></i>{{ $__t('Batteries settings') }}
					</a>
					@endif

					<div class="border-t border-gray-200 dark:border-gray-700 my-1"></div>

					{{-- User management --}}
					<a href="{{ $U('/usersettings') }}" data-href="{{ $U('/usersettings') }}" class="dropdown-item link-return">
						<i class="fa-solid fa-fw fa-user-cog mr-2"></i>{{ $__t('User settings') }}
					</a>

					<a href="{{ $U('/users') }}" class="dropdown-item permission-USERS_READ">
						<i class="fa-solid fa-fw fa-users mr-2"></i>{{ $__t('Manage users') }}
					</a>

					<div class="border-t border-gray-200 dark:border-gray-700 my-1"></div>

					{{-- Developer/API options --}}
					@if(!GROCY_DISABLE_AUTH)
					<a href="{{ $U('/manageapikeys') }}" class="dropdown-item">
						<i class="fa-solid fa-fw fa-handshake mr-2"></i>{{ $__t('Manage API keys') }}
					</a>
					@endif

					<a href="{{ $U('/api') }}" target="_blank" class="dropdown-item">
						<i class="fa-solid fa-fw fa-book mr-2"></i>{{ $__t('REST API browser') }}
					</a>

					<a href="{{ $U('/barcodescannertesting') }}" class="dropdown-item">
						<i class="fa-solid fa-fw fa-barcode mr-2"></i>{{ $__t('Barcode scanner testing') }}
					</a>

					<div class="border-t border-gray-200 dark:border-gray-700 my-1"></div>

					<a href="{{ $U('/about?embedded') }}" class="dropdown-item show-as-dialog-link" data-dialog-type="wider">
						<i class="fa-solid fa-fw fa-info mr-2"></i>{{ $__t('About Grocy') }}
					</a>
				</div>
			</div>
		</div>
	</div>
</header>

