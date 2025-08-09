<template>
  <div class="w-full bg-gray-50 min-h-full">
    <!-- Header with back button -->
    <header class="flex items-center p-4 border-b border-gray-200">
      <button class="text-xl mr-4" @click="$router.go(-1)">
        <font-awesome-icon :icon="['fas', 'arrow-left']" />
      </button>
      <h1 class="text-lg font-bold">Search</h1>
    </header>

    <!-- Search Bar -->
    <div class="p-4">
      <div class="flex items-center bg-gray-100 rounded-full px-4 py-2">
        <font-awesome-icon :icon="['fas', 'search']" class="text-gray-500 mr-2" />
        <input 
          ref="searchInput"
          type="text" 
          placeholder="Search events..." 
          class="flex-1 bg-transparent outline-none text-sm"
          v-model="searchQuery" 
          @input="handleSearch"
        >
        <button 
          v-if="searchQuery"
          @click="clearSearch"
          class="text-gray-400 hover:text-gray-600 ml-2"
        >
          <font-awesome-icon :icon="['fas', 'times']" />
        </button>
        <button 
          class="bg-primary text-white text-xs px-3 py-1 rounded-full ml-2" 
          @click="showFilters = true"
        >
          <font-awesome-icon :icon="['fas', 'filter']" class="mr-1" />
          Filters
        </button>
      </div>
    </div>

    <!-- Search Results -->
    <div class="px-4 pb-16">
      <!-- Loading State -->
      <div v-if="loading" class="text-center text-gray-500 py-8">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary mx-auto mb-2"></div>
        Loading events...
      </div>
      
      <!-- No Results -->
      <div v-else-if="events.length === 0 && searchQuery" class="text-center text-gray-500 py-8">
        <div class="mb-4">
          <font-awesome-icon :icon="['fas', 'search']" class="text-4xl text-gray-300" />
        </div>
        <h3 class="text-lg font-semibold mb-2">No events found</h3>
        <p>Try searching for "{{ searchQuery }}" with different keywords</p>
      </div>

      <!-- Initial State - Show some events -->
      <div v-else-if="events.length === 0 && !searchQuery" class="text-center text-gray-500 py-8">
        <div class="mb-4">
          <font-awesome-icon :icon="['fas', 'calendar-alt']" class="text-4xl text-gray-300" />
        </div>
        <h3 class="text-lg font-semibold mb-2">Discover Events</h3>
        <p>Search for events or browse categories</p>
      </div>

      <!-- Results Header -->
      <div v-if="events.length > 0" class="mb-4">
        <h2 class="text-lg font-bold text-text-main">
          {{ searchQuery ? `Results for "${searchQuery}"` : 'Featured Events' }}
        </h2>
        <p class="text-sm text-gray-600">{{ events.length }} event{{ events.length !== 1 ? 's' : '' }} found</p>
      </div>

      <!-- Events List -->
      <div v-if="events.length > 0" class="space-y-4">
        <CardEventList
          v-for="event in events"
          :key="event.id"
          :eventID="event.id"
          :imageUrl="getImageUrl(event.image)"
          :dateDay="new Date(event.start_time).getDate()"
          :dateMonth="new Date(event.start_time).toLocaleString('en-US', { month: 'short' }).toUpperCase()"
          :title="event.title"
          :location="event.location"
          :time="formatTime(event.start_time)"
        />
      </div>
    </div>

    <!-- Filter Modal -->
    <FilterModal 
      v-if="showFilters" 
      @close="showFilters = false"
      @apply="applyFilters"
      :currentFilters="appliedFilters"
    />
  </div>
</template>

<script>
import api from '@/api';
import CardEventList from '@/components/CardEventList.vue';
import FilterModal from '@/components/FilterModal.vue';
import { library } from '@fortawesome/fontawesome-svg-core';
import { faArrowLeft, faSearch, faFilter, faTimes, faCalendarAlt } from '@fortawesome/free-solid-svg-icons';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';

library.add(faArrowLeft, faSearch, faFilter, faTimes, faCalendarAlt);

export default {
  components: {
    CardEventList,
    FilterModal,
    FontAwesomeIcon
  },
  data() {
    return {
      searchQuery: '',
      events: [],
      loading: false,
      showFilters: false,
      appliedFilters: {
        categories: [],
        time: '',
        location: '',
        priceRange: [0, 500]
      },
      searchTimeout: null
    };
  },
  mounted() {
    // Auto-focus the search input
    this.$refs.searchInput.focus();
    // Load some initial events
    this.fetchInitialEvents();
  },
  methods: {
    async fetchInitialEvents() {
      this.loading = true;
      try {
        // Get some random/featured events to show initially
        const response = await api.get('/events/random');
        this.events = response.data.data || [];
      } catch (error) {
        console.error('Failed to fetch initial events:', error);
        this.events = [];
      } finally {
        this.loading = false;
      }
    },

    handleSearch() {
      // Clear previous timeout
      clearTimeout(this.searchTimeout);
      
      // If search is empty, show initial events
      if (!this.searchQuery.trim()) {
        this.fetchInitialEvents();
        return;
      }

      // Debounce search to avoid too many API calls
      this.searchTimeout = setTimeout(() => {
        this.fetchEvents();
      }, 300);
    },

    async fetchEvents() {
      this.loading = true;
      try {
        const params = new URLSearchParams();
        
        if (this.searchQuery.trim()) {
          params.append('query', this.searchQuery.trim());
        }
        
        // Add filter parameters
        if (this.appliedFilters.categories.length > 0) {
          params.append('categories', this.appliedFilters.categories.join(','));
        }
        
        if (this.appliedFilters.time) {
          params.append('time_filter', this.appliedFilters.time);
        }
        
        if (this.appliedFilters.location) {
          params.append('location', this.appliedFilters.location);
        }
        
        params.append('min_price', this.appliedFilters.priceRange[0]);
        params.append('max_price', this.appliedFilters.priceRange[1]);

        const response = await api.get(`/events/search?${params.toString()}`);
        this.events = response.data.data || [];
      } catch (error) {
        console.error('Failed to fetch events:', error);
        this.events = [];
      } finally {
        this.loading = false;
      }
    },

    clearSearch() {
      this.searchQuery = '';
      this.fetchInitialEvents();
      this.$refs.searchInput.focus();
    },

    applyFilters(filters) {
      this.appliedFilters = { ...filters };
      if (this.searchQuery.trim()) {
        this.fetchEvents();
      } else {
        this.fetchInitialEvents();
      }
    },

    getImageUrl(image) {
      if (!image) {
        return 'https://placehold.co/300x200/E0F2F7/2C3E50?text=Event';
      }
      if (image.startsWith('http')) {
        return image;
      }
      return `http://localhost:8000/storage/${image}`;
    },

    formatTime(dateString) {
      const date = new Date(dateString);
      return date.toLocaleTimeString('en-US', { 
        hour: '2-digit', 
        minute: '2-digit' 
      });
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

.text-text-main {
  color: rgb(var(--color-text-main));
}
</style>