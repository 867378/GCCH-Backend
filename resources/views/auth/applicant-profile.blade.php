@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto mt-10 bg-white p-6 rounded shadow">
    <h2 class="text-xl font-bold mb-4">Complete Your Applicant Profile</h2>
    <form method="POST" action="{{ route('applicant.profile', ['user' => $user->id]) }}">
        @csrf
        <div class="grid grid-cols-1 gap-4">
            <input name="first_name" placeholder="First Name" class="p-2 border rounded" required>
            <input name="middle_name" placeholder="Middle Name (optional)" class="p-2 border rounded">
            <input name="last_name" placeholder="Last Name" class="p-2 border rounded" required>
            <input type="date" name="date_of_birth" class="p-2 border rounded" required>
            <select name="gender" class="p-2 border rounded" required>
                <option value="">-- Select Gender --</option>
                <option value="male">Male</option>
                <option value="female">Female</option>
                <option value="other">Other</option>
            </select>
            <input name="phone_number" placeholder="Phone Number" class="p-2 border rounded" required>
            <input name="course" placeholder="Course" class="p-2 border rounded" required>
        </div>
        <button type="submit" class="w-full mt-4 bg-green-600 text-white p-2 rounded hover:bg-green-700">
            Submit Profile
        </button>
    </form>
</div>
@endsection
