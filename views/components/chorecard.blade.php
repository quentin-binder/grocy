@once
@push('componentScripts')
<script src="{{ $U('/viewjs/components/chorecard.js', true) }}?v={{ $version }}"></script>
@endpush
@endonce

@php if(!isset($asModal)) { $asModal = false; } @endphp

@if($asModal)
<div x-data="{ open: false }"
	x-show="open"
	@chorecard-modal-open.window="open = true"
	id="chorecard-modal"
	class="fixed inset-0 z-50 overflow-y-auto"
	x-cloak>
	<div class="flex items-center justify-center min-h-screen px-4">
		<div class="fixed inset-0 bg-gray-500 dark:bg-gray-900 bg-opacity-75 dark:bg-opacity-75 transition-opacity"
			@click="open = false"></div>
		<div class="card relative max-w-lg w-full text-center">
			<div class="p-4">
				@endif

				<div class="card chorecard">
					<div class="card-header flex justify-between items-center">
						<span>{{ $__t('Chore overview') }}</span>
						<div class="flex gap-1">
							<a id="chorecard-chore-journal-button"
								class="btn-ghost text-sm px-3 py-1 disabled show-as-dialog-link"
								href="#"
								data-dialog-type="table">
								{{ $__t('Chore journal') }}
							</a>
							<a id="chorecard-chore-edit-button"
								class="btn-ghost text-sm px-3 py-1 disabled"
								href="#"
								data-tooltip="{{ $__t('Edit chore') }}">
								<i class="fa-solid fa-edit"></i>
							</a>
						</div>
					</div>
					<div class="card-body">
						<h3><span id="chorecard-chore-name"></span></h3>

						<p id="chorecard-chore-description"
							class="text-gray-500 dark:text-gray-400 mt-0"></p>

						<strong>{{ $__t('Tracked count') }}:</strong> <span id="chorecard-chore-tracked-count"
							class="locale-number locale-number-generic"></span><br>
						<strong>{{ $__t('Average execution frequency') }}:</strong> <span id="chorecard-average-execution-frequency"></span><br>
						<strong>{{ $__t('Last tracked') }}:</strong> <span id="chorecard-chore-last-tracked"></span> <time id="chorecard-chore-last-tracked-timeago"
							class="timeago timeago-contextual"></time><br>
						@if(GROCY_FEATURE_FLAG_CHORES_ASSIGNMENTS)
						<strong>{{ $__t('Last done by') }}:</strong> <span id="chorecard-chore-last-done-by"></span>
						@endif
					</div>
				</div>

				@if($asModal)
			</div>
			<div class="flex justify-end gap-2 px-4 pb-4 border-t border-gray-200 dark:border-gray-700 pt-3">
				<button type="button"
					class="btn-secondary"
					@click="open = false">{{ $__t('Close') }}</button>
			</div>
		</div>
	</div>
</div>
@endif
