@php require_frontend_packages(['bootstrap-combobox']); @endphp

@once
@push('componentScripts')
<script src="{{ $U('/viewjs/components/userpicker.js', true) }}?v={{ $version }}"></script>
@endpush
@endonce

@php if(empty($prefillByUsername)) { $prefillByUsername = ''; } @endphp
@php if(empty($prefillByUserId)) { $prefillByUserId = ''; } @endphp
@php if(!isset($nextInputSelector)) { $nextInputSelector = ''; } @endphp

<div class="mb-4"
	data-next-input-selector="{{ $nextInputSelector }}"
	data-prefill-by-username="{{ $prefillByUsername }}"
	data-prefill-by-user-id="{{ $prefillByUserId }}">
	<label for="user_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ $__t($label) }}</label>
	<select class="select w-full user-combobox"
		id="user_id"
		name="user_id">
		<option value=""></option>
		@foreach($users as $user)
		<option data-additional-searchdata="{{ $user->username }}"
			value="{{ $user->id }}">{{ GetUserDisplayName($user) }}</option>
		@endforeach
	</select>
</div>
