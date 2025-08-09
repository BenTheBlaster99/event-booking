<template>
    <div
        class="min-h-screen w-full max-w-[480px] mx-auto p-6 flex flex-col justify-center items-center font-sans bg-gray-50">
        <!-- Header -->
        <div class="w-full flex items-center mb-8">
            <button @click="$router.go(-1)" class="text-xl">
                <font-awesome-icon :icon="['fas', 'arrow-left']" />
            </button>
            <h1 class="text-lg font-bold text-center flex-1">Reset Password</h1>
        </div>

        <!-- Reset Password Icon -->
        <div class="w-24 h-24 bg-primary-lighter rounded-full flex items-center justify-center mb-6">
            <font-awesome-icon :icon="['fas', 'lock']" class="text-3xl text-primary" />
        </div>

        <!-- Title and Description -->
        <div class="text-center mb-8">
            <h2 class="text-2xl font-bold text-text-main mb-4">Forgot Password?</h2>
            <p class="text-gray-600 leading-relaxed">
                Don't worry! It occurs. Please enter the email address linked with your account.
            </p>
        </div>

        <!-- Error Display -->
        <div v-if="error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4 w-full">
            {{ error }}
        </div>

        <!-- Success Display -->
        <div v-if="success"
            class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-4 w-full">
            {{ success }}
        </div>

        <!-- Email Form -->
        <form @submit.prevent="handlePasswordReset" class="w-full space-y-6">
            <!-- Email Input -->
            <div class="space-y-2">
                <input v-model="email" type="email" placeholder="Enter your email"
                    class="w-full h-14 px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-lighter"
                    required>
            </div>

            <!-- Send Code Button -->
            <button type="submit" :disabled="loading"
                class="w-full h-14 bg-primary text-white py-3 rounded-lg font-semibold shadow-lg transition-all duration-200 hover:bg-primary-light disabled:opacity-50 disabled:cursor-not-allowed">
                <span v-if="loading">Sending...</span>
                <span v-else>SEND CODE</span>
            </button>
        </form>

        <!-- Back to Sign In -->
        <div class="text-center mt-6">
            <p class="text-gray-600 text-sm">
                Remember your password?
                <router-link to="/signin" class="text-primary font-semibold hover:underline">
                    Sign In
                </router-link>
            </p>
        </div>
    </div>
</template>

<script>
import api from '@/api';
import { library } from '@fortawesome/fontawesome-svg-core';
import { faArrowLeft, faLock } from '@fortawesome/free-solid-svg-icons';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';

library.add(faArrowLeft, faLock);

export default {
    components: {
        FontAwesomeIcon
    },
    data() {
        return {
            email: '',
            loading: false,
            error: '',
            success: ''
        };
    },
    methods: {
        async handlePasswordReset() {
            this.error = '';
            this.success = '';
            this.loading = true;

            try {
                const response = await api.post('/password/forgot', {
                    email: this.email
                });

                this.success = 'Password reset code sent to your email!';

                // Store email for the reset password page
                localStorage.setItem('reset_email', this.email);

                // Redirect to reset password page after 2 seconds
                setTimeout(() => {
                    this.$router.push('/reset-password');
                }, 2000);

            } catch (error) {
                console.error('Password reset error:', error);
                this.error = error.response?.data?.message || 'Failed to send reset code. Please try again.';
            } finally {
                this.loading = false;
            }
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

.focus\:ring-primary-lighter:focus {
    --tw-ring-color: rgb(var(--color-primary-lighter));
}
</style>