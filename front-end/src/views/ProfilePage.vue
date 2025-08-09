<template>
  <div class="max-w-[480px] mx-auto min-h-screen bg-gray-50 font-sans">
    <!-- Header -->
    <header class="p-4 flex items-center justify-between bg-white shadow-sm">
      <button @click="$router.push('/home')" class="text-xl">
        <font-awesome-icon :icon="['fas', 'arrow-left']" />
      </button>
      <h1 class="text-xl font-bold">Profile</h1>
      <button @click="showOptionsMenu = !showOptionsMenu" class="text-xl relative">
        <font-awesome-icon :icon="['fas', 'ellipsis-v']" />
        
        <!-- Options Dropdown -->
        <div v-if="showOptionsMenu" class="absolute right-0 top-8 bg-white rounded-lg shadow-lg border border-gray-200 py-2 w-48 z-20">
          <button @click="editProfile" class="w-full text-left px-4 py-2 hover:bg-gray-50 flex items-center">
            <font-awesome-icon :icon="['fas', 'user-edit']" class="mr-3 text-primary" />
            Edit Profile
          </button>
          <button @click="viewBookmarks" class="w-full text-left px-4 py-2 hover:bg-gray-50 flex items-center">
            <font-awesome-icon :icon="['fas', 'bookmark']" class="mr-3 text-primary" />
            Bookmarks
          </button>
          <hr class="my-2">
          <button @click="handleLogout" class="w-full text-left px-4 py-2 hover:bg-red-50 text-red-500 flex items-center">
            <font-awesome-icon :icon="['fas', 'sign-out-alt']" class="mr-3" />
            Logout
          </button>
        </div>
      </button>
    </header>

    <!-- Profile Content -->
    <div v-if="loading" class="p-4 text-center text-gray-500">
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary mx-auto mb-2"></div>
      <p>Loading profile...</p>
    </div>

    <div v-else-if="user" class="pb-20">
      <!-- User Info -->
      <div class="p-4">
        <div class="flex items-center space-x-4 mb-6 bg-white rounded-lg p-4 shadow-sm">
          <img
            :src="user.avatar || 'https://placehold.co/80x80/E0F2F7/2C3E50?text=P'"
            alt="User Avatar"
            class="w-20 h-20 rounded-full object-cover"
          >
          <div class="flex-1">
            <h2 class="text-xl font-bold">{{ user.name }}</h2>
            <p class="text-gray-500 text-sm">{{ user.email }}</p>
            <p v-if="user.bio" class="text-gray-600 text-sm mt-1">{{ user.bio }}</p>
          </div>
        </div>

        <!-- Stats Section -->
        <div class="bg-white rounded-lg shadow-sm p-4 mb-4">
          <div class="grid grid-cols-3 gap-4 text-center">
            <div>
              <p class="text-2xl font-bold text-primary">{{ userStats.eventsCreated }}</p>
              <p class="text-sm text-gray-600">Events</p>
            </div>
            <div>
              <p class="text-2xl font-bold text-primary">{{ userStats.bookmarks }}</p>
              <p class="text-sm text-gray-600">Bookmarks</p>
            </div>
            <div>
              <p class="text-2xl font-bold text-primary">{{ userStats.reviews }}</p>
              <p class="text-sm text-gray-600">Reviews</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Tab Navigation -->
      <div class="px-4 mb-4">
        <div class="bg-white rounded-lg shadow-sm p-1 flex">
          <button 
            @click="activeTab = 'about'"
            class="flex-1 py-2 px-4 rounded-lg font-medium transition-all duration-200"
            :class="activeTab === 'about' ? 'bg-primary text-white shadow-md' : 'text-gray-600 hover:text-primary'"
          >
            About
          </button>
          <button 
            @click="activeTab = 'events'"
            class="flex-1 py-2 px-4 rounded-lg font-medium transition-all duration-200"
            :class="activeTab === 'events' ? 'bg-primary text-white shadow-md' : 'text-gray-600 hover:text-primary'"
          >
            Events
          </button>
          <button 
            @click="activeTab = 'reviews'"
            class="flex-1 py-2 px-4 rounded-lg font-medium transition-all duration-200"
            :class="activeTab === 'reviews' ? 'bg-primary text-white shadow-md' : 'text-gray-600 hover:text-primary'"
          >
            Reviews
          </button>
        </div>
      </div>

      <!-- Tab Content -->
      <div class="px-4">
        <!-- About Tab -->
        <div v-if="activeTab === 'about'" class="bg-white rounded-lg shadow-sm p-4">
          <h3 class="text-lg font-bold text-text-main mb-3">About</h3>
          <p v-if="user.bio" class="text-gray-600 leading-relaxed mb-4">
            {{ user.bio }}
          </p>
          <p v-else class="text-gray-500 italic mb-4">
            No bio available. Click "Edit Profile" to add one!
          </p>
          
          <div class="space-y-3">
            <div class="flex items-center text-sm text-gray-600">
              <font-awesome-icon :icon="['fas', 'envelope']" class="mr-3 text-primary" />
              {{ user.email }}
            </div>
            <div class="flex items-center text-sm text-gray-600">
              <font-awesome-icon :icon="['fas', 'calendar']" class="mr-3 text-primary" />
              Joined {{ formatDate(user.created_at) }}
            </div>
          </div>
        </div>

        <!-- Events Tab -->
        <div v-if="activeTab === 'events'">
          <ProfileEvents />
        </div>

        <!-- Reviews Tab -->
        <div v-if="activeTab === 'reviews'">
          <ProfileReviews />
        </div>
      </div>
    </div>

    <div v-else class="p-4 text-center text-gray-500">
      <font-awesome-icon :icon="['fas', 'exclamation-triangle']" class="text-4xl text-red-400 mb-4" />
      <p class="mb-4">Could not load profile. Please try again.</p>
      <button @click="fetchProfile" class="bg-primary text-white px-4 py-2 rounded-lg font-semibold">
        Retry
      </button>
    </div>

    <!-- Click outside to close menu -->
    <div v-if="showOptionsMenu" @click="showOptionsMenu = false" class="fixed inset-0 z-10"></div>
  </div>
</template>

<script>
import api from '@/api';
import ProfileEvents from './ProfileEvents.vue';
import ProfileReviews from './ProfileReviews.vue'; 
import { library } from '@fortawesome/fontawesome-svg-core';
import { 
  faArrowLeft, 
  faEllipsisV,
  faUserEdit, 
  faBookmark, 
  faSignOutAlt,
  faExclamationTriangle,
  faEnvelope,
  faCalendar
} from '@fortawesome/free-solid-svg-icons';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';

library.add(faArrowLeft, faEllipsisV, faUserEdit, faBookmark, faSignOutAlt, faExclamationTriangle, faEnvelope, faCalendar);

export default {
  components: {
    FontAwesomeIcon,
    ProfileEvents,
    ProfileReviews
  },
  data() {
    return {
      user: null,
      loading: false,
      activeTab: 'about',
      showOptionsMenu: false,
      userStats: {
        eventsCreated: 0,
        bookmarks: 0,
        reviews: 0
      }
    };
  },
  methods: {
    async fetchProfile() {
      this.loading = true;
      try {
        const response = await api.get('/profile');
        console.log('Profile response:', response.data);
        
        let userData = null;
        
        if (response.data.success && response.data.data) {
          // Structure: { success: true, data: { user data } }
          userData = response.data.data;
        } else if (response.data.data && response.data.data.data) {
          // Structure: { data: { data: { user data } } }
          userData = response.data.data.data;
        } else if (response.data.data) {
          // Structure: { data: { user data } }
          userData = response.data.data;
        } else {
          // Direct structure: { user data }
          userData = response.data;
        }
        
        console.log('Processed user data:', userData);
        
        if (userData && userData.name) {
          this.user = {
            ...userData,
            avatar: userData.avatar 
              ? `http://localhost:8000/storage/${userData.avatar}` 
              : 'https://placehold.co/80x80/E0F2F7/2C3E50?text=P'
          };
          
          // Fetch user stats
          await this.fetchUserStats();
        } else {
          throw new Error(response.data.message || 'Failed to fetch profile');
        }
      } catch (error) {
        console.error('Failed to fetch profile:', error);
        this.user = null;
      } finally {
        this.loading = false;
      }
    },

    async fetchUserStats() {
      try {
        // Fetch events created by user
        const eventsResponse = await api.get('/user/events');
        if (eventsResponse.data.success) {
          this.userStats.eventsCreated = eventsResponse.data.data.length;
        }

        // Fetch bookmarks
        const bookmarksResponse = await api.get('/bookmarks');
        if (bookmarksResponse.data.success) {
          this.userStats.bookmarks = bookmarksResponse.data.data.length;
        }

        // Fetch reviews count
        const reviewsResponse = await api.get('/user/reviews');
        if (reviewsResponse.data.success) {
          this.userStats.reviews = reviewsResponse.data.data.length;
        }
      } catch (error) {
        console.error('Failed to fetch user stats:', error);
      }
    },

    editProfile() {
      this.showOptionsMenu = false;
      this.$router.push('/home/profile/edit');
    },

    viewBookmarks() {
      this.showOptionsMenu = false;
      this.$router.push('/home/profile/bookmarks');
    },

    async handleLogout() {
      this.showOptionsMenu = false;
      try {
        await api.post('/logout');
      } catch (error) {
        console.error('Logout failed:', error);
      } finally {
        localStorage.removeItem('token');
        this.$router.push('/signin');
      }
    },

    formatDate(dateString) {
      if (!dateString) return 'Recently';
      const date = new Date(dateString);
      return date.toLocaleDateString('en-US', { 
        year: 'numeric', 
        month: 'long' 
      });
    }
  },
  created() {
    this.fetchProfile();
  },
};
</script>

<style scoped>
.text-primary {
  color: rgb(var(--color-primary));
}

.bg-primary {
  background-color: rgb(var(--color-primary));
}

.text-text-main {
  color: rgb(var(--color-text-main));
}

.hover\:text-primary:hover {
  color: rgb(var(--color-primary));
}
</style>