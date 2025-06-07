@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto mt-10 bg-white p-6 rounded shadow">
    <h2 class="text-xl font-bold mb-4">Select Your Role</h2>
    <form method="POST" action="{{ route('set.role', ['user' => $user->id]) }}">
        @csrf
        <div class="mb-4">
            <label for="role" class="block text-sm font-medium text-gray-700">Role</label>
            <select name="role" id="role" required class="w-full border border-gray-300 rounded p-2 mt-1">
                <option value="">-- Select Role --</option>
                <option value="applicant">Applicant</option>
                <option value="company">Company</option>
            </select>
        </div>
        <button type="submit" class="w-full bg-blue-600 text-white p-2 rounded hover:bg-blue-700">
            Continue
        </button>
    </form>
</div>
@endsection
