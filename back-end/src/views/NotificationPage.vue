<template>
  <div class="max-w-[480px] mx-auto min-h-screen bg-gray-50 font-sans">
    <!-- Header -->
    <header class="p-4 flex items-center border-b border-gray-200">
      <button @click="$router.go(-1)" class="text-xl mr-4">
        <svg class="h-6 w-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
      </button>
      <h1 class="text-xl font-bold">Notifications</h1>
    </header>

    <!-- Notification List -->
    <div v-if="isLoading" class="p-4 text-center text-gray-500">
      Loading notifications...
    </div>

    <div v-else-if="notifications.length > 0" class="p-4 space-y-4">
      <div
        v-for="notification in notifications"
        :key="notification.id"
        class="bg-white rounded-lg shadow-sm p-4 flex items-start space-x-4"
      >
        <img
          :src="notification.userImage || 'https://placehold.co/48x48/E0F2F7/2C3E50?text=U'"
          alt="User Avatar"
          class="w-12 h-12 rounded-full object-cover flex-shrink-0"
        >
        <div class="flex-1">
          <div class="flex justify-between items-center">
            <p class="font-medium text-text-main">{{ notification.userName }} <span class="text-gray-500">{{ notification.message }}</span></p>
            <span class="text-xs text-gray-400 flex-shrink-0">{{ notification.timestamp }}</span>
          </div>
          <p v-if="notification.eventTitle" class="text-sm text-primary-light font-semibold mt-1">
            {{ notification.eventTitle }}
          </p>
        </div>
      </div>
    </div>

    <div v-else class="p-4 text-center text-gray-500">
      You have no new notifications.
    </div>
  </div>
</template>

<script>
import api from '@/api';

export default {
  data() {
    return {
      isLoading: false,
      notifications: []
    };
  },
  created() {
    this.fetchNotifications();
  },
  methods: {
    async fetchNotifications() {
      this.isLoading = true;
      try {
        // Assuming your backend has a /notifications endpoint
        const response = await api.get('/notifications');
        this.notifications = response.data.data; // Assuming data is in a 'data' property
      } catch (error) {
        console.error('Failed to fetch notifications:', error);
        this.notifications = [];
      } finally {
        this.isLoading = false;
      }
    }
  }
};
</script>

<style scoped>
/* Scoped styles to keep CSS local to this component */
.text-primary {
  color: rgb(var(--color-primary));
}
.bg-primary {
  background-color: rgb(var(--color-primary));
}
.text-primary-light {
  color: rgb(var(--color-primary-light));
}
.text-text-main {
  color: rgb(var(--color-text-main));
}
</style>
