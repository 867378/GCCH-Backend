import api from './api.js'; // Import the API module for making requests

console.log('Login is connected'); // Log to confirm the script is loaded

document.getElementById('login-form').addEventListener('submit', async (event) => {
    event.preventDefault(); // Prevent the default form submission

    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;

    api.post('/login', {
        email: email,
        password: password
    })
    .then(response => {
        const { token, redirect_url } = response.data;
        console.log('Redirect URL:', redirect_url); // Log the redirect URL for debugging
        localStorage.setItem('token', token); // Store the token in local storage
        window.location.href=redirect_url; // Redirect to the specified URL
    })
    .catch(error => {
        const errorMessage = error.response.data.message || 'An error occurred. Please try again.';
        document.getElementById('error-message').innerText = errorMessage; // Display the error message
    });
});
