<template>
  <div class="max-w-[480px] mx-auto min-h-screen bg-gray-50 font-sans">
    <!-- Header -->
    <header class="p-4 flex items-center justify-between border-b border-gray-200">
      <h1 class="text-xl font-bold">My Profile</h1>
      <button @click="handleLogout" class="text-sm text-red-500 font-medium">
        Logout
      </button>
    </header>

    <!-- Profile Content -->
    <div v-if="loading" class="p-4 text-center text-gray-500">
      Loading profile...
    </div>

    <div v-else-if="user" class="p-4">
      <!-- User Info -->
      <div class="flex items-center space-x-4 mb-6">
        <img
          :src="user.avatar || 'https://placehold.co/80x80/E0F2F7/2C3E50?text=P'"
          alt="User Avatar"
          class="w-20 h-20 rounded-full object-cover"
        >
        <div>
          <h2 class="text-xl font-bold">{{ user.name }}</h2>
          <p class="text-gray-500 text-sm">{{ user.email }}</p>
        </div>
      </div>

      <!-- Profile Actions/Links -->
      <nav class="space-y-4">
        <div class="bg-white rounded-lg shadow-sm p-4">
          <router-link to="/home/profile/edit" class="flex items-center justify-between py-2">
            <span class="font-medium">Edit Profile</span>
            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
          </router-link>
          <router-link to="/home/profile/settings" class="flex items-center justify-between py-2 border-t border-gray-100">
            <span class="font-medium">Settings</span>
            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
          </router-link>
          <router-link to="/home/profile/bookmarks" class="flex items-center justify-between py-2 border-t border-gray-100">
            <span class="font-medium">Bookmarks</span>
            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
          </router-link>
        </div>
      </nav>
    </div>

    <div v-else class="p-4 text-center text-gray-500">
      Could not load profile. Please try again.
    </div>
  </div>
</template>

<script>
import api from '@/api';

export default {
  data() {
    return {
      user: null,
      loading: false,
    };
  },
  methods: {
    async fetchProfile() {
      this.loading = true;
      try {
        const response = await api.get('/profile');
        this.user = response.data.data; // Assuming your API returns data in a 'data' property
      } catch (error) {
        console.error('Failed to fetch profile:', error);
        this.user = null;
      } finally {
        this.loading = false;
      }
    },
    async handleLogout() {
      try {
        await api.post('/logout');
      } catch (error) {
        console.error('Logout failed:', error);
      } finally {
        // Even if the API call fails, clear the token and redirect to ensure logout
        localStorage.removeItem('token');
        this.$router.push('/signin');
      }
    }
  },
  created() {
    this.fetchProfile();
  },
};
</script>

<style scoped>
/* Scoped styles for the profile page */
</style>
