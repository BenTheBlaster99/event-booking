<template>
  <div class="space-y-4">
    <!-- Loading State -->
    <div v-if="loading" class="text-center py-8 bg-white rounded-lg shadow-sm">
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary mx-auto mb-2"></div>
      <p class="text-gray-600">Loading reviews...</p>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="text-center py-8 bg-white rounded-lg shadow-sm">
      <font-awesome-icon :icon="['fas', 'exclamation-triangle']" class="text-4xl text-red-400 mb-4" />
      <p class="text-gray-600 mb-4">{{ error }}</p>
      <button @click="fetchReviews" class="bg-primary text-white px-4 py-2 rounded-lg font-semibold">
        Try Again
      </button>
    </div>

    <!-- Reviews List -->
    <div v-else-if="reviews.length > 0">
      <div v-for="review in reviews" :key="review.id" class="bg-white rounded-lg shadow-sm p-4 flex items-start">
        <img :src="getAvatarUrl(review.user?.avatar)" alt="Reviewer Avatar"
          class="w-10 h-10 rounded-full object-cover mr-4 flex-shrink-0">
        <div class="flex-1">
          <div class="flex justify-between items-center mb-1">
            <h3 class="font-semibold text-text-main">{{ review.user?.name || 'Anonymous' }}</h3>
            <span class="text-gray-500 text-xs">{{ formatDate(review.created_at) }}</span>
          </div>
          <div class="flex items-center mb-2">
            <!-- Star Rating -->
            <div class="flex">
              <font-awesome-icon v-for="n in 5" :key="n" :icon="['fas', 'star']" class="text-sm"
                :class="n <= review.rating ? 'text-yellow-400' : 'text-gray-300'" />
            </div>
            <span class="ml-2 text-sm text-gray-600">({{ review.rating }}/5)</span>
          </div>
          <p class="text-gray-600 text-sm leading-snug">{{ review.comment }}</p>

          <!-- Event Info (if available) -->
          <div v-if="review.event" class="mt-2 text-xs text-gray-500 bg-gray-50 rounded px-2 py-1 inline-block">
            Review for: {{ review.event.title }}
          </div>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div v-else class="text-center py-8 bg-white rounded-lg shadow-sm">
      <font-awesome-icon :icon="['fas', 'star']" class="text-4xl text-gray-300 mb-4" />
      <h3 class="text-lg font-semibold text-gray-600 mb-2">No Reviews Yet</h3>
      <p class="text-gray-500">Reviews from event attendees will appear here.</p>
    </div>
  </div>
</template>

<script>
import api from '@/api';
import { library } from '@fortawesome/fontawesome-svg-core';
import { faStar, faExclamationTriangle } from '@fortawesome/free-solid-svg-icons';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';

library.add(faStar, faExclamationTriangle);

export default {
  name: 'ProfileReviews',
  components: {
    FontAwesomeIcon
  },
  data() {
    return {
      reviews: [],
      loading: false,
      error: null
    };
  },
  async created() {
    await this.fetchReviews();
  },
  methods: {
    async fetchReviews() {
      this.loading = true;
      this.error = null;

      try {
        console.log('Fetching user reviews...');
        const response = await api.get('/user/reviews');
        console.log('Reviews response:', response.data.data);

        // Handle different response structures
        let reviewsData = [];

        if (response.data.success && response.data.data) {
          reviewsData = response.data.data;
        } else if (response.data.data && response.data.data.data) {
          reviewsData = response.data.data.data;
        } else if (response.data.data) {
          reviewsData = response.data.data;
        } else if (Array.isArray(response.data)) {
          reviewsData = response.data;
        }

        console.log('Processed reviews data:', reviewsData);

        this.reviews = Array.isArray(reviewsData) ? reviewsData : [];
      } catch (error) {
        console.error('Failed to fetch reviews:', error);
        this.error = error.response?.data?.message || error.message || 'Failed to load reviews';
        this.reviews = [];
      } finally {
        this.loading = false;
      }
    },

    getAvatarUrl(avatar) {
      if (!avatar) {
        return 'https://placehold.co/40x40/E0E0E0/808080?text=👤';
      }
      if (avatar.startsWith('http')) {
        return avatar;
      }
      return `http://localhost:8000/storage/${avatar}`;
    },

    formatDate(dateString) {
      if (!dateString) return 'Recently';
      const date = new Date(dateString);
      const now = new Date();
      const diffTime = Math.abs(now - date);
      const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

      if (diffDays === 1) return 'Yesterday';
      if (diffDays < 7) return `${diffDays} days ago`;
      if (diffDays < 30) return `${Math.ceil(diffDays / 7)} weeks ago`;

      return date.toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: date.getFullYear() !== now.getFullYear() ? 'numeric' : undefined
      });
    }
  }
}
</script>

<style scoped>
.text-text-main {
  color: rgb(var(--color-text-main));
}

.text-primary {
  color: rgb(var(--color-primary));
}

.bg-primary {
  background-color: rgb(var(--color-primary));
}
</style>