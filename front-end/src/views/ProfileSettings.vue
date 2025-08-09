<template>
    <div class="max-w-[480px] mx-auto min-h-screen bg-gray-50 font-sans">
      <!-- Header -->
      <header class="p-4 flex items-center border-b border-gray-200">
        <button @click="$router.go(-1)" class="text-xl">
          <font-awesome-icon :icon="['fas', 'arrow-left']" />
        </button>
        <h1 class="text-lg font-bold ml-4">Settings</h1>
      </header>
  
      <!-- Settings Content -->
      <div class="p-4 space-y-6">
        <!-- Notification Settings -->
        <div class="bg-white rounded-lg shadow-sm p-4">
          <h2 class="font-bold text-lg mb-3">Notifications</h2>
          <div class="space-y-4">
            <div class="flex items-center justify-between">
              <span>Event Reminders</span>
              <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" class="sr-only peer" v-model="settings.notifications.eventReminders">
                <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
              </label>
            </div>
            <div class="flex items-center justify-between">
              <span>New Messages</span>
              <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" class="sr-only peer" v-model="settings.notifications.newMessages">
                <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
              </label>
            </div>
          </div>
        </div>
  
        <!-- Account Settings -->
        <div class="bg-white rounded-lg shadow-sm p-4">
          <h2 class="font-bold text-lg mb-3">Account</h2>
          <div class="space-y-4">
            <button 
              @click="changePassword"
              class="w-full text-left py-2 border-b border-gray-100"
            >
              Change Password
            </button>
            <button 
              @click="deleteAccount"
              class="w-full text-left py-2 text-red-500"
            >
              Delete Account
            </button>
          </div>
        </div>
  
        <!-- Save Button -->
        <button 
          @click="saveSettings"
          class="w-full bg-primary text-white py-3 rounded-lg font-semibold"
        >
          Save Settings
        </button>
      </div>
    </div>
  </template>
  
  <script>
  import api from '@/api';
  
  export default {
    data() {
      return {
        settings: {
          notifications: {
            eventReminders: true,
            newMessages: true
          }
        }
      };
    },
    methods: {
      async saveSettings() {
        try {
          await api.put('/profile/settings', this.settings);
          this.$router.go(-1);
        } catch (error) {
          console.error('Error saving settings:', error);
        }
      },
      changePassword() {
        this.$router.push('/reset-password');
      },
      deleteAccount() {
        if (confirm('Are you sure you want to delete your account? This cannot be undone.')) {
          // Implement account deletion
        }
      }
    }
  };
  </script>