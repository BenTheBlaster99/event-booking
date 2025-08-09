<template>
    <div class="min-h-screen w-full max-w-[480px] mx-auto p-6 flex flex-col justify-center items-center font-sans bg-gray-50">
      <!-- Header -->
      <div class="w-full flex items-center mb-8">
        <button @click="$router.go(-1)" class="text-xl">
          <font-awesome-icon :icon="['fas', 'arrow-left']" />
        </button>
        <h1 class="text-lg font-bold text-center flex-1">Verification</h1>
      </div>
  
      <!-- Verification Icon -->
      <div class="w-24 h-24 bg-primary-lighter rounded-full flex items-center justify-center mb-6">
        <font-awesome-icon :icon="['fas', 'envelope']" class="text-3xl text-primary" />
      </div>
  
      <!-- Title and Description -->
      <div class="text-center mb-8">
        <h2 class="text-2xl font-bold text-text-main mb-4">Check Your Email</h2>
        <p class="text-gray-600 leading-relaxed">
          We've sent a verification code to<br>
          <span class="font-semibold text-text-main">{{ email }}</span>
        </p>
      </div>
  
      <!-- Error Display -->
      <div v-if="error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4 w-full">
        {{ error }}
      </div>
  
      <!-- Success Display -->
      <div v-if="success" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-4 w-full">
        {{ success }}
      </div>
  
      <!-- Verification Code Input -->
      <form @submit.prevent="handleVerification" class="w-full space-y-6">
        <div class="flex justify-center space-x-3">
          <input
            v-for="(digit, index) in verificationCode"
            :key="index"
            :ref="`digit-${index}`"
            v-model="verificationCode[index]"
            type="text"
            maxlength="1"
            class="w-12 h-12 text-center text-xl font-bold border-2 border-gray-200 rounded-lg focus:outline-none focus:border-primary"
            @input="handleDigitInput(index, $event)"
            @keydown="handleKeyDown(index, $event)"
          >
        </div>
  
        <!-- Verify Button -->
        <button
          type="submit"
          :disabled="loading || !isCodeComplete"
          class="w-full h-14 bg-primary text-white py-3 rounded-lg font-semibold shadow-lg transition-all duration-200 hover:bg-primary-light disabled:opacity-50 disabled:cursor-not-allowed"
        >
          <span v-if="loading">Verifying...</span>
          <span v-else>VERIFY</span>
        </button>
      </form>
  
      <!-- Resend Code -->
      <div class="text-center mt-6">
        <p class="text-gray-600 text-sm mb-2">Didn't receive the code?</p>
        <button
          @click="resendCode"
          :disabled="resendLoading || resendCooldown > 0"
          class="text-primary font-semibold text-sm hover:underline disabled:opacity-50 disabled:cursor-not-allowed"
        >
          <span v-if="resendLoading">Sending...</span>
          <span v-else-if="resendCooldown > 0">Resend in {{ resendCooldown }}s</span>
          <span v-else>Resend Code</span>
        </button>
      </div>
    </div>
  </template>
  
  <script>
  import api from '@/api';
  import { library } from '@fortawesome/fontawesome-svg-core';
  import { faArrowLeft, faEnvelope } from '@fortawesome/free-solid-svg-icons';
  import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
  
  library.add(faArrowLeft, faEnvelope);
  
  export default {
    components: {
      FontAwesomeIcon
    },
    data() {
      return {
        email: '',
        verificationCode: ['', '', '', '', '', ''],
        loading: false,
        resendLoading: false,
        resendCooldown: 0,
        error: '',
        success: '',
        resendTimer: null
      };
    },
    computed: {
      isCodeComplete() {
        return this.verificationCode.every(digit => digit !== '');
      },
      codeString() {
        return this.verificationCode.join('');
      }
    },
    created() {
      // Get email from route params or localStorage
      this.email = this.$route.params.email || localStorage.getItem('verification_email') || 'your email';
    },
    methods: {
      handleDigitInput(index, event) {
        const value = event.target.value;
        
        // Only allow numbers
        if (!/^\d*$/.test(value)) {
          this.verificationCode[index] = '';
          return;
        }
  
        this.verificationCode[index] = value;
  
        // Auto-focus next input
        if (value && index < 5) {
          this.$refs[`digit-${index + 1}`][0].focus();
        }
      },
  
      handleKeyDown(index, event) {
        // Handle backspace
        if (event.key === 'Backspace' && !this.verificationCode[index] && index > 0) {
          this.$refs[`digit-${index - 1}`][0].focus();
        }
      },
  
      async handleVerification() {
        this.error = '';
        this.success = '';
        this.loading = true;
  
        try {
          const response = await api.post('/email/verify', {
            email: this.email,
            verification_code: this.codeString
          });
  
          this.success = 'Email verified successfully!';
          
          // Store token if provided
          if (response.data.token) {
            localStorage.setItem('token', response.data.token);
          }
  
          // Redirect after success
          setTimeout(() => {
            this.$router.push('/home');
          }, 2000);
  
        } catch (error) {
          console.error('Verification error:', error);
          this.error = error.response?.data?.message || 'Verification failed. Please try again.';
          
          // Clear the code on error
          this.verificationCode = ['', '', '', '', '', ''];
          this.$refs['digit-0'][0].focus();
        } finally {
          this.loading = false;
        }
      },
  
      async resendCode() {
        this.resendLoading = true;
        this.error = '';
  
        try {
          await api.post('/email/resend-verification', {
            email: this.email
          });
  
          this.success = 'Verification code sent!';
          this.startResendCooldown();
  
        } catch (error) {
          console.error('Resend error:', error);
          this.error = error.response?.data?.message || 'Failed to resend code. Please try again.';
        } finally {
          this.resendLoading = false;
        }
      },
  
      startResendCooldown() {
        this.resendCooldown = 60;
        this.resendTimer = setInterval(() => {
          this.resendCooldown--;
          if (this.resendCooldown <= 0) {
            clearInterval(this.resendTimer);
          }
        }, 1000);
      }
    },
  
    beforeUnmount() {
      if (this.resendTimer) {
        clearInterval(this.resendTimer);
      }
    }
  };
  </script>
  
  <style scoped>
  .text-primary {
    color: rgb(var(--color-primary));
  }
  
  .bg-primary {
    background-color: rgb(var(--color-primary));
  }
  
  .bg-primary-lighter {
    background-color: rgb(var(--color-primary-lighter));
  }
  
  .text-text-main {
    color: rgb(var(--color-text-main));
  }
  
  .hover\:bg-primary-light:hover {
    background-color: rgb(var(--color-primary-light));
  }
  
  .focus\:border-primary:focus {
    border-color: rgb(var(--color-primary));
  }
  </style>