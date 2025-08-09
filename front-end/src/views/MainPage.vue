<template>
  <!-- Adjusted pt- to account for the new, taller header (179px) -->
  <div class="w-full bg-gray-50 min-h-screen font-sans pt-[100px] pb-[111px]">
    <main class="p-3">
      <!-- Upcoming Events Section -->
      <section class="px-3 mt-4">
        <div class="flex justify-between items-center mb-3" style="width: 326.71px; height: 34px; border-radius: 1px;">
          <h2 class="font-bold text-base text-text-main">Upcoming Events</h2>
          <button 
            @click="$router.push('/home/all-events')" 
            class="text-primary text-xs font-medium hover:underline"
          >
            See all
          </button>
        </div>

        <div class="flex overflow-x-auto gap-4 pb-4 scrollbar-hide">
          <!-- Show a loading message while fetching data -->
          <div v-if="loadingEvents" class="text-gray-500 p-4 flex-shrink-0">Loading events...</div>
          <!-- Show a message if no events are found -->
          <div v-else-if="upcomingEvents.length === 0" class="text-gray-500 p-4 flex-shrink-0">
            No upcoming events found.
          </div>
          <!-- Loop through events fetched from the API -->
          <CardEvent v-for="event in upcomingEvents" :key="event.id" :eventID="event.id"
            :imageUrl="getImageUrl(event.image)"
            :dateDay="new Date(event.start_time).getDate()"
            :dateMonth="new Date(event.start_time).toLocaleString('default', { month: 'short' }).toUpperCase()"
            :title="event.title" :location="event.location" />
        </div>
      </section>

      <!-- Invite Your Friends Section -->
      <section
        class="mt-6 p-4 bg-gradient-to-r from-blue-100 to-blue-50 rounded-lg shadow-md flex items-center justify-between overflow-hidden relative"
        style="width: 328px; height: 127px; margin-left: auto; margin-right: auto;">
        <div class="z-10">
          <h3 class="font-bold text-lg text-primary-darkest mb-1">Invite your friends</h3>
          <p class="text-sm text-gray-700 mb-3">Get $20 for ticket</p>
          <button
            class="bg-primary text-white px-6 py-2 rounded-lg font-semibold shadow-md hover:bg-primary-light transition-colors duration-200"
            style="width: 153px; height: 48px;">
            INVITE
          </button>
        </div>
        <img src="https://placehold.co/150x120/E0F2F7/2C3E50?text=Gift" alt="Gift Illustration"
          class="absolute right-0 top-0 h-full object-cover opacity-70">
      </section>

      <!-- Nearby You Section -->
      <section class="mt-6 px-3">
        <div class="flex justify-between items-center mb-3">
          <h2 class="font-bold text-base text-text-main">Nearby You</h2>
          <button 
            @click="$router.push('/home/map')" 
            class="text-primary text-xs font-medium hover:underline"
          >
            See all
          </button>
        </div>
        
        <div class="flex overflow-x-auto gap-4 pb-4 scrollbar-hide">
          <div v-if="loadingNearby" class="text-gray-500 p-4 flex-shrink-0">Loading nearby events...</div>
          <div v-else-if="nearbyEvents.length === 0" class="text-gray-500 p-4 flex-shrink-0">
            No nearby events found.
          </div>
          <CardEvent v-for="event in nearbyEvents" :key="event.id" :eventID="event.id"
            :imageUrl="getImageUrl(event.image)"
            :dateDay="new Date(event.start_time).getDate()"
            :dateMonth="new Date(event.start_time).toLocaleString('default', { month: 'short' }).toUpperCase()"
            :title="event.title" :location="event.location" />
        </div>
      </section>

    </main>
  </div>
</template>

<script>
import api from '@/api';
import CardEvent from '@/components/CardEvent.vue';

export default {
  components: {
    CardEvent,
  },
  data() {
    return {
      upcomingEvents: [],
      nearbyEvents: [],
      selectedCategory: null,
      loadingEvents: false,
      loadingNearby: false,
    };
  },
  async created() {
    console.log("Components created. Starting data fetching...");
    await Promise.all([
      this.fetchUpcomingEvents(),
      this.fetchNearbyEvents()
    ]);
    console.log("All data fetch attempts completed.");
  },
  methods: {
    async fetchUpcomingEvents() {
      this.loadingEvents = true;
      try {
        const response = await api.get('/events/upcoming');
        console.log('Upcoming events response:', response.data);
        
        // Take only first 5 events for the main page
        this.upcomingEvents = (response.data.data.data || []).slice(0, 5);
      } catch (error) {
        console.error('Failed to fetch upcoming events:', error);
        this.upcomingEvents = [];
      } finally {
        this.loadingEvents = false;
      }
    },

    async fetchNearbyEvents() {
      this.loadingNearby = true;
      try {
        // For now, fetch events with location data
        const response = await api.get('/events');
        const allEvents = response.data.data || [];
        
        // Filter events that have location data and take first 5
        this.nearbyEvents = allEvents
          .filter(event => event.latitude && event.longitude)
          .slice(0, 5);
      } catch (error) {
        console.error('Failed to fetch nearby events:', error);
        this.nearbyEvents = [];
      } finally {
        this.loadingNearby = false;
      }
    },

    getImageUrl(image) {
      if (!image) {
        return 'https://placehold.co/600x400/2C3E50/FFF?text=Event+Cover';
      }
      if (image.startsWith('http')) {
        return image;
      }
      return `http://localhost:8000/storage/${image}`;
    },

    selectCategory(categoryName) {
      this.selectedCategory = this.selectedCategory === categoryName ? null : categoryName;
      // You can add logic here to filter events by the selected category.
    }
  },
};
</script>

<style scoped>
/* Scoped styles to keep CSS local to this component */
.scrollbar-hide::-webkit-scrollbar {
  display: none;
}

.scrollbar-hide {
  -ms-overflow-style: none;
  /* IE and Edge */
  scrollbar-width: none;
  /* Firefox */
}

.text-primary {
  color: rgb(var(--color-primary));
}

.bg-primary {
  background-color: rgb(var(--color-primary));
}

.bg-primary-light {
  background-color: rgb(var(--color-primary-light));
}

.text-text-main {
  color: rgb(var(--color-text-main));
}

.hover\:bg-primary-light:hover {
  background-color: rgb(var(--color-primary-light));
}
</style>