@once
@push('componentScripts')
<script src="{{ $U('/viewjs/components/batterycard.js', true) }}?v={{ $version }}"></script>
@endpush
@endonce

@php if(!isset($asModal)) { $asModal = false; } @endphp

@if($asModal)
<div x-data="{ open: false }"
	x-show="open"
	@batterycard-modal-open.window="open = true"
	id="batterycard-modal"
	class="fixed inset-0 z-50 overflow-y-auto"
	x-cloak>
	<div class="flex items-center justify-center min-h-screen px-4">
		<div class="fixed inset-0 bg-gray-500 dark:bg-gray-900 bg-opacity-75 dark:bg-opacity-75 transition-opacity"
			@click="open = false"></div>
		<div class="card relative max-w-lg w-full text-center">
			<div class="p-4">
				@endif

				<div class="card batterycard">
					<div class="card-header flex justify-between items-center">
						<span>{{ $__t('Battery overview') }}</span>
						<div class="flex gap-1">
							<a id="batterycard-battery-journal-button"
								class="btn-ghost text-sm px-3 py-1 disabled show-as-dialog-link"
								href="#"
								data-dialog-type="table">
								{{ $__t('Battery journal') }}
							</a>
							<a id="batterycard-battery-edit-button"
								class="btn-ghost text-sm px-3 py-1 disabled"
								href="#"
								data-tooltip="{{ $__t('Edit battery') }}">
								<i class="fa-solid fa-edit"></i>
							</a>
						</div>
					</div>
					<div class="card-body">
						<h3><span id="batterycard-battery-name"></span></h3>
						<strong>{{ $__t('Used in') }}:</strong> <span id="batterycard-battery-used_in"></span><br>
						<strong>{{ $__t('Charge cycles count') }}:</strong> <span id="batterycard-battery-charge-cycles-count"
							class="locale-number locale-number-generic"></span><br>
						<strong>{{ $__t('Last charged') }}:</strong> <span id="batterycard-battery-last-charged"></span> <time id="batterycard-battery-last-charged-timeago"
							class="timeago timeago-contextual"></time><br>
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
