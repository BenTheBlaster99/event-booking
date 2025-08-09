<!-- SignIn.vue -->
<template>
    <div class="min-h-screen w-full max-w-[480px] mx-auto p-6 flex flex-col justify-center items-center font-sans">
  
      <!-- Logo -->
      <!-- The original dimensions (162x114px) are preserved using w-40 h-28 and centered with flex -->
      <div class="flex justify-center mb-10">
        <img src="/src/images/logo.png" alt="Logo" class="w-40 h-28" />
      </div>
  
      <!-- Sign In Label -->
      <!-- "Sign in" label centered with a responsive font size -->
      <h1 class="text-3xl font-semibold text-center mb-8">Sign in</h1>
  
      <!-- Error Display -->
      <div v-if="error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        {{ error }}
      </div>
  
      <!-- Form -->
      <form @submit.prevent="handleSubmit" class="w-full space-y-5">
        <!-- Email Input -->
        <div class="space-y-2">
          <!-- The width is now w-full for responsiveness, with a specific height -->
          <input v-model="form.email" type="email" placeholder="abc@email.com"
            class="w-full h-14 px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-lighter"
            required>
        </div>
  
        <!-- Password Input -->
        <div class="space-y-2">
          <input v-model="form.password" type="password" placeholder="Password"
            class="w-full h-14 px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-lighter"
            required>
        </div>
  
        <!-- Remember Me & Forgot Password -->
        <div class="flex justify-between items-center text-sm mt-3">
          <label class="inline-flex items-center text-gray-600">
            <input type="checkbox" v-model="form.remember" class="form-checkbox text-primary-light h-4 w-4 rounded">
            <span class="ml-2">Remember Me</span>
          </label>
          <button type="button" class="text-primary font-medium text-xs">Forgot password?</button>
        </div>
  
        <!-- Sign In Button -->
        <!-- The width is w-full for responsiveness, height and spacing are adjusted -->
        <button type="submit"
          class="w-full h-14 bg-primary text-white py-3 rounded-lg font-semibold shadow-lg transition-all duration-200 hover:bg-primary-light">
          SIGN IN
        </button>
      </form>
  
      <!-- OR Divider -->
      <div class="flex items-center my-6">
        <div class="flex-1 border-t border-gray-200"></div>
        <div class="px-3 text-gray-500 text-sm">OR</div>
        <div class="flex-1 border-t border-gray-200"></div>
      </div>
  
      <!-- Social Login Buttons -->
      <div class="w-full space-y-4">
        <!-- Buttons are full-width, centered, and have a consistent height/style -->
        <button type="button"
          class="w-full h-14 border border-gray-200 py-3 rounded-lg font-medium flex items-center justify-center space-x-2 transition-all duration-200 hover:bg-gray-100">
          <svg class="h-5 w-5" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path
              d="M44.5 20H24V28.5H35.5C34.5 31.5 32.5 34.5 24 34.5C18.5 34.5 14 30 14 24.5C14 19 18.5 14.5 24 14.5C26.5 14.5 28.5 15.5 30 17L36.5 10.5C33.5 7.5 29.5 6.5 24 6.5C13.5 6.5 5.5 14.5 5.5 24.5C5.5 34.5 13.5 42.5 24 42.5C34.5 42.5 41.5 35.5 41.5 24.5C41.5 23 41.5 21.5 41 20H44.5Z"
              fill="#4285F4" />
            <path d="M5.5 24.5H14V24.5H5.5V24.5Z" fill="#34A853" />
            <path d="M5.5 24.5L14 24.5V24.5L5.5 24.5Z" fill="#FABC05" />
            <path d="M5.5 24.5L14 24.5V24.5L5.5 24.5Z" fill="#EA4335" />
          </svg>
          <span>Login with Google</span>
        </button>
  
        <button type="button"
          class="w-full h-14 border border-gray-200 py-3 rounded-lg font-medium flex items-center justify-center space-x-2 transition-all duration-200 hover:bg-gray-100">
          <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path
              d="M22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22C17.5228 22 22 17.5228 22 12ZM19.2968 12.0125C19.2968 16.0354 16.0354 19.2968 12.0125 19.2968C9.64539 19.2968 7.56846 18.1755 6.29688 16.4219V16.4219L7.96875 16.2969C8.94824 16.9609 10.0898 17.375 11.2969 17.375C14.041 17.375 16.2969 15.1191 16.2969 12.375C16.2969 11.2012 15.938 10.1064 15.3125 9.17969V9.17969L17.2969 9.125C18.2588 10.0527 19.2968 11.0967 19.2968 12.0125Z"
              fill="#1877F2" />
          </svg>
          <span>Login with Facebook</span>
        </button>
      </div>
  
      <!-- Footer link -->
      <div class="text-center mt-6 text-sm">
        Don't have an account?
        <a href="/signup" class="text-primary font-medium">Sign up</a>
      </div>
    </div>
  </template>
  
  <script>
  import api from '@/api';
  export default {
      data() {
          return {
              form: {
                  email: '',
                  password: '',
                  remember: false
              },
              error: '',
              loading: false
          }
      },
      methods: {
          async handleSubmit() {
              this.error = '';
              this.loading = true;
              try {
                  // Assuming the CSRF cookie is needed, based on the previous snippet
                  await api.get('/sanctum/csrf-cookie');
                  const response = await api.post('/login', {
                      email: this.form.email,
                      password: this.form.password
                  });
                  localStorage.setItem('token', response.data.token);
                  this.$router.push('/home');
              } catch (error) {
                  console.error('Login error:', error);
                  this.error = error.response?.data?.message || 'Login failed';
              } finally {
                  this.loading = false;
              }
          }
      }
  };
  </script>
  