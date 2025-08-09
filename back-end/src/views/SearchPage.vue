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
            type="text" 
            placeholder="Search events..." 
            class="flex-1 bg-transparent outline-none text-sm"
            v-model="searchQuery" 
            @input="handleSearch"
          >
          <button 
            class="bg-primary text-white text-xs px-3 py-1 rounded-full ml-2" 
            @click="showFilters = true"
          >
            <font-awesome-icon :icon="['fas', 'filter']" class="mr-1" />
            Filters
          </button>
        </div>
      </div>
  
      <!-- Active Filters Display -->
      <div v-if="hasActiveFilters" class="px-4 pb-2">
        <div class="flex flex-wrap gap-2">
          <span 
            v-for="filter in activeFilters" 
            :key="filter.key"
            class="bg-primary text-white text-xs px-2 py-1 rounded-full flex items-center"
          >
            {{ filter.label }}
            <button @click="removeFilter(filter.key)" class="ml-1">
              <font-awesome-icon :icon="['fas', 'times']" class="text-xs" />
            </button>
          </span>
          <button 
            @click="clearAllFilters" 
            class="text-primary text-xs underline"
          >
            Clear all
          </button>
        </div>
      </div>
  
      <!-- Search Results -->
      <div class="px-4 pb-16">
        <div v-if="loading" class="text-center text-gray-500 py-8">
          <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary mx-auto mb-2"></div>
          Loading search results...
        </div>
        
        <div v-else-if="events.length === 0 && searchQuery" class="text-center text-gray-500 py-8">
          <div class="mb-4">
            <font-awesome-icon :icon="['fas', 'search']" class="text-4xl text-gray-300" />
          </div>
          <h3 class="text-lg font-semibold mb-2">No events found</h3>
          <p>Try adjusting your search or filters</p>
        </div>
  
        <div v-else-if="events.length === 0" class="text-center text-gray-500 py-8">
          <div class="mb-4">
            <font-awesome-icon :icon="['fas', 'calendar-alt']" class="text-4xl text-gray-300" />
          </div>
          <h3 class="text-lg font-semibold mb-2">Discover Events</h3>
          <p>Search for events or browse by category</p>
        </div>
  
        <div v-else class="space-y-4">
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
    computed: {
      hasActiveFilters() {
        return this.activeFilters.length > 0;
      },
      activeFilters() {
        const filters = [];
        
        if (this.appliedFilters.categories.length > 0) {
          filters.push({
            key: 'categories',
            label: `Categories: ${this.appliedFilters.categories.join(', ')}`
          });
        }
        
        if (this.appliedFilters.time) {
          filters.push({
            key: 'time',
            label: `Time: ${this.appliedFilters.time}`
          });
        }
        
        if (this.appliedFilters.location) {
          filters.push({
            key: 'location',
            label: `Location: ${this.appliedFilters.location}`
          });
        }
        
        return filters;
      }
    },
    methods: {
      handleSearch() {
        // Debounce search to avoid too many API calls
        clearTimeout(this.searchTimeout);
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
  
          const endpoint = this.searchQuery.trim() || this.hasActiveFilters 
            ? '/events/search' 
            : '/events/featured';
            
          const response = await api.get(`${endpoint}?${params.toString()}`);
          this.events = response.data.data || [];
        } catch (error) {
          console.error('Failed to fetch events:', error);
          this.events = [];
        } finally {
          this.loading = false;
        }
      },
  
      applyFilters(filters) {
        this.appliedFilters = { ...filters };
        this.fetchEvents();
      },
  
      removeFilter(filterKey) {
        switch (filterKey) {
          case 'categories':
            this.appliedFilters.categories = [];
            break;
          case 'time':
            this.appliedFilters.time = '';
            break;
          case 'location':
            this.appliedFilters.location = '';
            break;
        }
        this.fetchEvents();
      },
  
      clearAllFilters() {
        this.appliedFilters = {
          categories: [],
          time: '',
          location: '',
          priceRange: [0, 500]
        };
        this.fetchEvents();
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
    },
  
    created() {
      this.fetchEvents();
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