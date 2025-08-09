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
            <font-awesome-icon :icon="['fas', 'key']" class="text-3xl text-primary" />
        </div>

        <!-- Title and Description -->
        <div class="text-center mb-8">
            <h2 class="text-2xl font-bold text-text-main mb-4">Create New Password</h2>
            <p class="text-gray-600 leading-relaxed">
                Your new password must be unique from those previously used.
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

        <!-- Reset Form -->
        <form @submit.prevent="handleResetPassword" class="w-full space-y-6">
            <!-- Reset Code Input -->
            <div class="space-y-2">
                <input v-model="resetCode" type="text" placeholder="Enter reset code"
                    class="w-full h-14 px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-lighter"
                    required>
            </div>

            <!-- New Password Input -->
            <div class="space-y-2">
                <input v-model="newPassword" type="password" placeholder="New Password"
                    class="w-full h-14 px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-lighter"
                    required>
            </div>

            <!-- Confirm Password Input -->
            <div class="space-y-2">
                <input v-model="confirmPassword" type="password" placeholder="Confirm Password"
                    class="w-full h-14 px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-lighter"
                    required>
            </div>

            <!-- Password Requirements -->
            <div class="text-sm text-gray-600 space-y-1">
                <p class="font-medium">Password must contain:</p>
                <ul class="space-y-1 ml-4">
                    <li class="flex items-center">
                        <span :class="passwordValidation.minLength ? 'text-green-600' : 'text-gray-400'">
                            {{ passwordValidation.minLength ? '✓' : '○' }}
                        </span>
                        <span class="ml-2">At least 8 characters</span>
                    </li>
                    <li class="flex items-center">
                        <span :class="passwordValidation.hasUppercase ? 'text-green-600' : 'text-gray-400'">
                            {{ passwordValidation.hasUppercase ? '✓' : '○' }}
                        </span>
                        <span class="ml-2">One uppercase letter</span>
                    </li>
                    <li class="flex items-center">
                        <span :class="passwordValidation.hasLowercase ? 'text-green-600' : 'text-gray-400'">
                            {{ passwordValidation.hasLowercase ? '✓' : '○' }}
                        </span>
                        <span class="ml-2">One lowercase letter</span>
                    </li>
                    <li class="flex items-center">
                        <span :class="passwordValidation.hasNumber ? 'text-green-600' : 'text-gray-400'">
                            {{ passwordValidation.hasNumber ? '✓' : '○' }}
                        </span>
                        <span class="ml-2">One number</span>
                    </li>
                </ul>
            </div>

            <!-- Reset Password Button -->
            <button type="submit" :disabled="loading || !isFormValid"
                class="w-full h-14 bg-primary text-white py-3 rounded-lg font-semibold shadow-lg transition-all duration-200 hover:bg-primary-light disabled:opacity-50 disabled:cursor-not-allowed">
                <span v-if="loading">Resetting...</span>
                <span v-else>RESET PASSWORD</span>
            </button>
        </form>
    </div>
</template>

<script>
import api from '@/api';
import { library } from '@fortawesome/fontawesome-svg-core';
import { faArrowLeft, faKey } from '@fortawesome/free-solid-svg-icons';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';

library.add(faArrowLeft, faKey);

export default {
    components: {
        FontAwesomeIcon
    },
    data() {
        return {
            email: '',
            resetCode: '',
            newPassword: '',
            confirmPassword: '',
            loading: false,
            error: '',
            success: ''
        };
    },
    computed: {
        passwordValidation() {
            return {
                minLength: this.newPassword.length >= 8,
                hasUppercase: /[A-Z]/.test(this.newPassword),
                hasLowercase: /[a-z]/.test(this.newPassword),
                hasNumber: /\d/.test(this.newPassword)
            };
        },
        isPasswordValid() {
            return Object.values(this.passwordValidation).every(Boolean);
        },
        passwordsMatch() {
            return this.newPassword === this.confirmPassword;
        },
        isFormValid() {
            return this.resetCode && this.isPasswordValid && this.passwordsMatch;
        }
    },
    created() {
        this.email = localStorage.getItem('reset_email') || '';
    },
    methods: {
        async handleResetPassword() {
            this.error = '';
            this.success = '';

            if (!this.passwordsMatch) {
                this.error = 'Passwords do not match.';
                return;
            }

            if (!this.isPasswordValid) {
                this.error = 'Password does not meet requirements.';
                return;
            }

            this.loading = true;

            try {
                const response = await api.post('/password/reset', {
                    email: this.email,
                    token: this.resetCode,
                    password: this.newPassword,
                    password_confirmation: this.confirmPassword
                });

                this.success = 'Password reset successfully!';

                // Clear stored email
                localStorage.removeItem('reset_email');

                // Redirect to sign in after success
                setTimeout(() => {
                    this.$router.push('/signin');
                }, 2000);

            } catch (error) {
                console.error('Password reset error:', error);
                this.error = error.response?.data?.message || 'Failed to reset password. Please try again.';
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