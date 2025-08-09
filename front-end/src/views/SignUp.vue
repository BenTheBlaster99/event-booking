<template>
  <div class="min-h-screen w-full max-w-[480px] mx-auto p-6 flex flex-col font-sans bg-gray-50 text-text-main">
    <!-- Logo placeholder -->
    <div class="text-center mb-8">
      <div class="h-12 flex items-center justify-center">
        <!-- Replaced "LOGO" with a simple SVG for better visual appeal -->
        <svg class="h-10 w-10 text-primary" fill="currentColor" viewBox="0 0 20 20">
          <path d="M10 2a8 8 0 100 16 8 8 0 000-16zM8 13a1 1 0 001 1h2a1 1 0 100-2H9a1 1 0 00-1 1zM9 8h2a1 1 0 000-2H9a1 1 0 000 2zM12.586 5.586a2 2 0 112.828 2.828L12.828 11H15a1 1 0 100-2h-2a1 1 0 00-1 1v2a1 1 0 102 0V11a3 3 0 00-3-3H9a3 3 0 00-3 3v2a1 1 0 102 0V11a1 1 0 001-1z" />
        </svg>
      </div>
    </div>

    <h1 class="text-2xl font-semibold text-center mb-8">Sign up</h1>

    <!-- Error Display -->
    <div v-if="error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4">
      {{ error }}
    </div>

    <!-- Loading Indicator -->
    <div v-if="loading" class="text-center p-4">
      <p>Signing up...</p>
    </div>

    <!-- Form -->
    <form @submit.prevent="handleRegister" class="space-y-4">
      <!-- Full Name Input -->
      <div class="space-y-2">
        <input v-model="form.fullName" type="text" placeholder="Full name"
          class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-light" required>
      </div>

      <!-- Email Input -->
      <div class="space-y-2">
        <input v-model="form.email" type="email" placeholder="abc@email.com"
          class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-light" required>
      </div>

      <!-- Password Input -->
      <div class="space-y-2">
        <input v-model="form.password" type="password" placeholder="Password"
          class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-light" required>
      </div>

      <!-- Confirm Password Input -->
      <div class="space-y-2">
        <input v-model="form.confirmPassword" type="password" placeholder="Confirm password"
          class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-light" required>
      </div>

      <!-- Sign Up Button -->
      <button type="submit" :disabled="loading"
        class="w-full bg-primary text-white py-3 rounded-lg font-semibold shadow-lg hover:bg-primary-light transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
        SIGN UP
      </button>

      <!-- OR Divider -->
      <div class="flex items-center my-4">
        <div class="flex-1 border-t border-gray-200"></div>
        <div class="px-3 text-gray-500 text-sm">OR</div>
        <div class="flex-1 border-t border-gray-200"></div>
      </div>

      <!-- Social Login Buttons -->
      <div class="space-y-3">
        <button type="button"
          class="w-full border border-gray-200 py-3 rounded-lg font-medium flex items-center justify-center text-text-main shadow-sm hover:bg-gray-100 transition-colors duration-200">
          <!-- Google Icon placeholder -->
          <img src="https://www.svgrepo.com/show/303108/google-icon-logo.svg" alt="Google" class="w-5 h-5 mr-2">
          <span>Login with Google</span>
        </button>
        <button type="button"
          class="w-full border border-gray-200 py-3 rounded-lg font-medium flex items-center justify-center text-text-main shadow-sm hover:bg-gray-100 transition-colors duration-200">
          <!-- Facebook Icon placeholder -->
          <img src="https://www.svgrepo.com/show/303111/facebook-icon-logo.svg" alt="Facebook" class="w-5 h-5 mr-2">
          <span>Login with Facebook</span>
        </button>
      </div>
    </form>

    <!-- Footer link -->
    <div class="text-center mt-6 text-sm">
      Already have an account?
      <router-link to="/signin" class="text-primary font-medium hover:underline">Sign in</router-link>
    </div>
  </div>
</template>

<script>
// NOTE: Assuming '@/api' correctly imports an axios instance configured with the base URL.
// The provided `api.js` file seems to handle this.
import api from '@/api';

export default {
  data() {
    return {
      form: {
        fullName: '',
        email: '',
        password: '',
        confirmPassword: ''
      },
      error: '',
      loading: false
    };
  },
  methods: {
    async handleRegister() {
      // Clear previous error message and set loading state
      this.error = '';
      this.loading = true;

      // Simple password validation
      if (this.form.password !== this.form.confirmPassword) {
        this.error = 'Passwords do not match.';
        this.loading = false;
        return;
      }

      try {
        // Correctly calling the register API endpoint
        const response = await api.post('/register', {
          name: this.form.fullName,
          email: this.form.email,
          password: this.form.password,
          password_confirmation: this.form.confirmPassword // Laravel Sanctum expects this field
        });

        // Assuming a successful registration returns a token or success message
        // You may need to adjust this based on your backend's response structure
        if (response.data.token) {
          localStorage.setItem('token', response.data.token);
          this.$router.push('/home');
        } else {
          // If no token is returned, but the request was successful, redirect to signin
          // to let the user log in.
          this.$router.push('/signin');
        }
        
      } catch (error) {
        console.error('Registration error:', error);
        // Display a user-friendly error message
        this.error = error.response?.data?.message || 'Registration failed. Please try again.';

        // If the backend returns validation errors, display them.
        if (error.response?.data?.errors) {
          this.error = Object.values(error.response.data.errors).flat().join(' ');
        }
      } finally {
        // Reset loading state
        this.loading = false;
      }
    }
  }
};
</script>

<style scoped>
/*
 * We can define custom CSS variables in main.css and reference them here.
 * The Tailwind classes should pick up the theme values automatically.
 */
.text-primary { color: rgb(var(--color-primary)); }
.bg-primary { background-color: rgb(var(--color-primary)); }
.hover\:bg-primary-light:hover { background-color: rgb(var(--color-primary-light)); }
.focus\:ring-primary-light:focus { --tw-ring-color: rgb(var(--color-primary-light)); }

</style>
