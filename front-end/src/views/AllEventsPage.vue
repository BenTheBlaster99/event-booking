<template>
  <div class="w-full h-full bg-gray-50 flex flex-col">
      <!-- Header -->
      <header class="p-4 bg-white shadow-sm flex items-center justify-between">
          <button @click="$router.go(-1)" class="text-xl">
              <font-awesome-icon :icon="['fas', 'arrow-left']" />
          </button>
          <h1 class="text-lg font-bold text-text-main">My Events</h1>
          <button class="text-xl">
              <font-awesome-icon :icon="['fas', 'ellipsis-v']" />
          </button>
      </header>

      <!-- Tab Navigation -->
      <div class="p-4 bg-white shadow-sm flex justify-center space-x-2 mb-4">
          <button @click="activeTab = 'upcoming'"
              class="px-4 py-2 rounded-full text-sm font-medium transition-all duration-200"
              :class="activeTab === 'upcoming' ? 'bg-primary text-white shadow-md' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'">
              Upcoming
              <span v-if="upcomingEvents.length > 0"
                  class="ml-1 bg-white bg-opacity-20 px-1.5 py-0.5 rounded-full text-xs">
                  {{ upcomingEvents.length }}
              </span>
          </button>
          <button @click="activeTab = 'past'"
              class="px-4 py-2 rounded-full text-sm font-medium transition-all duration-200"
              :class="activeTab === 'past' ? 'bg-accent-orange text-white shadow-md' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'">
              Past Events
              <span v-if="pastEvents.length > 0"
                  class="ml-1 bg-white bg-opacity-20 px-1.5 py-0.5 rounded-full text-xs">
                  {{ pastEvents.length }}
              </span>
          </button>
          <button @click="activeTab = 'all'"
              class="px-4 py-2 rounded-full text-sm font-medium transition-all duration-200"
              :class="activeTab === 'all' ? 'bg-accent-green text-white shadow-md' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'">
              All Events
              <span v-if="allEvents.length > 0"
                  class="ml-1 bg-white bg-opacity-20 px-1.5 py-0.5 rounded-full text-xs">
                  {{ allEvents.length }}
              </span>
          </button>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="flex-1 flex items-center justify-center">
          <div class="text-center">
              <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary mx-auto mb-2"></div>
              <p class="text-gray-600">Loading your events...</p>
          </div>
      </div>

      <!-- Error State -->
      <div v-else-if="error" class="flex-1 flex items-center justify-center">
          <div class="text-center p-4">
              <font-awesome-icon :icon="['fas', 'exclamation-triangle']" class="text-4xl text-red-400 mb-4" />
              <h3 class="text-lg font-semibold text-gray-600 mb-2">Error Loading Events</h3>
              <p class="text-gray-500 mb-4">{{ error }}</p>
              <button @click="fetchEvents"
                  class="bg-primary text-white px-4 py-2 rounded-lg font-semibold hover:bg-primary-light transition-colors">
                  Try Again
              </button>
          </div>
      </div>

      <!-- Content Area -->
      <div v-else class="flex-1 overflow-y-auto">
          <!-- Upcoming Events Tab -->
          <div v-if="activeTab === 'upcoming'" class="p-4">
              <div v-if="upcomingEvents.length === 0"
                  class="flex flex-col items-center justify-center py-16 text-center">
                  <div class="w-20 h-20 bg-primary-lighter rounded-full flex items-center justify-center mb-4">
                      <font-awesome-icon :icon="['fas', 'calendar-plus']" class="text-2xl text-primary" />
                  </div>
                  <h2 class="text-xl font-bold text-text-main mb-2">No Upcoming Events</h2>
                  <p class="text-gray-600 mb-6 max-w-xs">You don't have any upcoming events. Create or join exciting
                      events!</p>
                  <button @click="$router.push('/home')"
                      class="bg-primary text-white py-3 px-6 rounded-lg font-semibold shadow-md flex items-center justify-center hover:bg-primary-light transition-colors">
                      EXPLORE EVENTS
                      <font-awesome-icon :icon="['fas', 'arrow-right']" class="ml-2" />
                  </button>
              </div>
              <div v-else class="space-y-4">
                  <CardEventList v-for="event in upcomingEvents" :key="event.id" :eventID="event.id"
                      :imageUrl="getImageUrl(event.image)" :dateDay="new Date(event.start_time).getDate()"
                      :dateMonth="new Date(event.start_time).toLocaleString('en-US', { month: 'short' }).toUpperCase()"
                      :title="event.title" :location="event.location" :time="formatTime(event.start_time)" />
              </div>
          </div>

          <!-- Past Events Tab -->
          <div v-if="activeTab === 'past'" class="p-4">
              <div v-if="pastEvents.length === 0" class="flex flex-col items-center justify-center py-16 text-center">
                  <div
                      class="w-20 h-20 bg-accent-orange bg-opacity-20 rounded-full flex items-center justify-center mb-4">
                      <font-awesome-icon :icon="['fas', 'history']" class="text-2xl text-accent-orange" />
                  </div>
                  <h2 class="text-xl font-bold text-text-main mb-2">No Past Events</h2>
                  <p class="text-gray-600 mb-6 max-w-xs">You haven't created any events yet. Start creating!</p>
                  <button @click="$router.push('/home')"
                      class="bg-accent-orange text-white py-3 px-6 rounded-lg font-semibold shadow-md flex items-center justify-center hover:opacity-90 transition-opacity">
                      CREATE EVENT
                      <font-awesome-icon :icon="['fas', 'arrow-right']" class="ml-2" />
                  </button>
              </div>
              <div v-else class="space-y-4">
                  <div v-for="event in pastEvents" :key="event.id" class="relative">
                      <CardEventList :eventID="event.id" :imageUrl="getImageUrl(event.image)"
                          :dateDay="new Date(event.start_time).getDate()"
                          :dateMonth="new Date(event.start_time).toLocaleString('en-US', { month: 'short' }).toUpperCase()"
                          :title="event.title" :location="event.location" :time="formatTime(event.start_time)" />
                      <!-- Past Event Overlay -->
                      <div
                          class="absolute inset-0 bg-gray-500 bg-opacity-30 rounded-xl flex items-center justify-end pr-4">
                          <span class="bg-accent-orange text-white text-xs px-2 py-1 rounded-full font-semibold">
                              COMPLETED
                          </span>
                      </div>
                  </div>
              </div>
          </div>

          <!-- All Events Tab -->
          <div v-if="activeTab === 'all'" class="p-4">
              <div v-if="allEvents.length === 0" class="flex flex-col items-center justify-center py-16 text-center">
                  <div
                      class="w-20 h-20 bg-accent-green bg-opacity-20 rounded-full flex items-center justify-center mb-4">
                      <font-awesome-icon :icon="['fas', 'calendar']" class="text-2xl text-accent-green" />
                  </div>
                  <h2 class="text-xl font-bold text-text-main mb-2">No Events Found</h2>
                  <p class="text-gray-600 mb-6 max-w-xs">Start creating amazing events!</p>
                  <button @click="$router.push('/home')"
                      class="bg-accent-green text-white py-3 px-6 rounded-lg font-semibold shadow-md flex items-center justify-center hover:opacity-90 transition-opacity">
                      CREATE EVENT
                      <font-awesome-icon :icon="['fas', 'arrow-right']" class="ml-2" />
                  </button>
              </div>
              <div v-else class="space-y-4">
                  <div v-for="event in allEvents" :key="event.id" class="relative">
                      <CardEventList :eventID="event.id" :imageUrl="getImageUrl(event.image)"
                          :dateDay="new Date(event.start_time).getDate()"
                          :dateMonth="new Date(event.start_time).toLocaleString('en-US', { month: 'short' }).toUpperCase()"
                          :title="event.title" :location="event.location" :time="formatTime(event.start_time)" />
                      <!-- Event Status Badge -->
                      <div class="absolute top-2 right-2">
                          <span v-if="isEventPast(event.start_time)"
                              class="bg-accent-orange text-white text-xs px-2 py-1 rounded-full font-semibold">
                              PAST
                          </span>
                          <span v-else class="bg-primary text-white text-xs px-2 py-1 rounded-full font-semibold">
                              UPCOMING
                          </span>
                      </div>
                  </div>
              </div>
          </div>
      </div>
  </div>
</template>

<script>
import api from '@/api';
import CardEventList from '@/components/CardEventList.vue';
import { library } from '@fortawesome/fontawesome-svg-core';
import {
  faArrowLeft,
  faEllipsisV,
  faArrowRight,
  faCalendarPlus,
  faHistory,
  faCalendar,
  faExclamationTriangle
} from '@fortawesome/free-solid-svg-icons';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';

library.add(faArrowLeft, faEllipsisV, faArrowRight, faCalendarPlus, faHistory, faCalendar, faExclamationTriangle);

export default {
  name: 'EventsPage',
  components: {
      CardEventList,
      FontAwesomeIcon,
  },
  data() {
      return {
          activeTab: 'upcoming',
          upcomingEvents: [],
          pastEvents: [],
          allEvents: [],
          loading: false,
          error: null
      };
  },
  created() {
      this.fetchEvents();
  },
  methods: {
      async fetchEvents() {
          this.loading = true;
          this.error = null;
          
          try {
              console.log('Fetching user events...');
              const response = await api.get('/user/events');
              console.log('User events response:', response.data);
              
              if (response.data.success) {
                  const events = response.data.data || [];
                  const now = new Date();

                  // Separate events into upcoming and past
                  this.upcomingEvents = events.filter(event => new Date(event.start_time) > now);
                  this.pastEvents = events.filter(event => new Date(event.start_time) <= now);
                  this.allEvents = events;
                  
                  console.log(`Loaded ${events.length} user events (${this.upcomingEvents.length} upcoming, ${this.pastEvents.length} past)`);
              } else {
                  throw new Error(response.data.message || 'Failed to fetch user events');
              }
          } catch (error) {
              console.error('Failed to fetch user events:', error);
              this.error = error.response?.data?.message || error.message || 'Failed to load your events';
              
              // Reset arrays on error
              this.upcomingEvents = [];
              this.pastEvents = [];
              this.allEvents = [];
          } finally {
              this.loading = false;
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
      },

      isEventPast(dateString) {
          return new Date(dateString) <= new Date();
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

.bg-accent-orange {
  background-color: rgb(var(--color-accent-orange));
}

.text-accent-orange {
  color: rgb(var(--color-accent-orange));
}

.bg-accent-green {
  background-color: rgb(var(--color-accent-green));
}

.text-accent-green {
  color: rgb(var(--color-accent-green));
}

.hover\:bg-primary-light:hover {
  background-color: rgb(var(--color-primary-light));
}
</style>