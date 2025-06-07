@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto mt-10 bg-white p-6 rounded shadow">
    <h2 class="text-xl font-bold mb-4">Complete Your Company Profile</h2>
    <form method="POST" action="{{ route('company.profile', ['user' => $user->id]) }}">
        @csrf
        <div class="grid grid-cols-1 gap-4">
            <input name="company_name" placeholder="Company Name" class="p-2 border rounded" required>
            <input name="company_telephone" placeholder="Company Telephone" class="p-2 border rounded" required>
            <input name="street_address" placeholder="Street Address" class="p-2 border rounded" required>
            <input name="city" placeholder="City" class="p-2 border rounded" required>
            <input name="zip_code" placeholder="Zip Code" class="p-2 border rounded" required>
            <input name="country" placeholder="Country" class="p-2 border rounded" required>
            <input name="industry_type" placeholder="Industry Type" class="p-2 border rounded" required>
        </div>
        <button type="submit" class="w-full mt-4 bg-indigo-600 text-white p-2 rounded hover:bg-indigo-700">
            Submit Profile
        </button>
    </form>
</div>
@endsection
