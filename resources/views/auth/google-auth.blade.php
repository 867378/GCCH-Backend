@extends('layouts.app') {{-- Make sure you have a layouts.app blade with basic HTML structure --}}

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-100">
    <div class="bg-white shadow-xl rounded-2xl p-10 w-full max-w-md text-center">
        <h1 class="text-2xl font-bold mb-6 text-gray-800">Welcome!</h1>
        <p class="mb-6 text-gray-600">Sign in or sign up using your Google account.</p>

        <a href="{{ route('google.redirect') }}"
           class="inline-flex items-center px-4 py-2 bg-red-500 hover:bg-red-600 text-white text-sm font-medium rounded-lg transition duration-300">
            <svg class="w-5 h-5 mr-2" viewBox="0 0 488 512" fill="currentColor">
                <path d="M488 261.8c0-17.8-1.5-35-4.4-51.7H249v97.9h135.4c-5.9 31.4-23.7 58-50.6 75.7v62h81.9c48-44.2 75.3-109.3 75.3-183.9z"/>
                <path d="M249 492c67.5 0 124-22.4 165.3-60.8l-81.9-62c-22.7 15.3-51.6 24.3-83.4 24.3-64.1 0-118.4-43.2-137.9-101.5H28.4v63.6C69.9 439.6 152.9 492 249 492z"/>
                <path d="M111.1 297.9c-5.4-15.9-8.5-32.9-8.5-50.3s3.1-34.4 8.5-50.3v-63.6H28.4C10.1 164.8 0 204 0 247.6s10.1 82.8 28.4 113.9l82.7-63.6z"/>
                <path d="M249 97.9c35.2 0 66.9 12.1 91.8 35.8l68.7-68.7C373 27.4 316.5 0 249 0 152.9 0 69.9 52.4 28.4 134.8l82.7 63.6C130.6 141.1 184.9 97.9 249 97.9z"/>
            </svg>
            Continue with Google
        </a>
    </div>
</div>
@endsection
