<template>
  <div class="w-full h-full bg-gray-50 flex flex-col">
    <!-- Header -->
    <header class="p-4 bg-white shadow-sm flex items-center justify-between relative z-10">
      <button @click="$router.go(-1)" class="text-xl">
        <font-awesome-icon :icon="['fas', 'arrow-left']" />
      </button>
      <h1 class="text-lg font-bold text-text-main">Map</h1>
      <button @click="$router.push('/home/search')" class="text-xl">
        <font-awesome-icon :icon="['fas', 'search']" />
      </button>
    </header>

    <!-- Map Container -->
    <div class="flex-1 relative">
      <div id="map" class="w-full h-full"></div>

      <!-- Event Card Overlay (shows when marker is clicked) -->
      <div v-if="selectedEvent" class="absolute bottom-4 left-4 right-4 z-20">
        <CardEventMap :imageUrl="getImageUrl(selectedEvent.image)" :title="selectedEvent.title"
          :location="selectedEvent.location" :time="formatEventTime(selectedEvent.start_time)"
          @click="goToEventDetail(selectedEvent.id)" />
      </div>

      <!-- Loading overlay -->
      <div v-if="loading" class="absolute inset-0 bg-white bg-opacity-75 flex items-center justify-center z-10">
        <div class="text-center">
          <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary mx-auto mb-2"></div>
          <p class="text-gray-600">Loading events...</p>
        </div>
      </div>

      <!-- No events with location message -->
      <div v-if="!loading && events.length === 0" class="absolute inset-0 flex items-center justify-center z-10">
        <div class="text-center p-4">
          <font-awesome-icon :icon="['fas', 'map-marker-alt']" class="text-4xl text-gray-300 mb-4" />
          <h3 class="text-lg font-semibold text-gray-600 mb-2">No Events with Location</h3>
          <p class="text-gray-500 mb-4">No events have location coordinates available</p>
          <button @click="refreshEvents"
            class="bg-primary text-white px-4 py-2 rounded-lg font-semibold hover:bg-primary-light transition-colors">
            Refresh
          </button>
        </div>
      </div>

      <!-- Events counter -->
      <div v-if="events.length > 0" class="absolute top-4 left-4 bg-white rounded-lg shadow-md px-3 py-2 z-10">
        <span class="text-sm font-medium text-gray-700">
          {{ events.length }} event{{ events.length !== 1 ? 's' : '' }} found
        </span>
      </div>
    </div>
  </div>
</template>

<script>
import L from 'leaflet';
import api from '@/api';
import CardEventMap from '@/components/CardEventMap.vue';
import { library } from '@fortawesome/fontawesome-svg-core';
import { faArrowLeft, faSearch, faMapMarkerAlt } from '@fortawesome/free-solid-svg-icons';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';

library.add(faArrowLeft, faSearch, faMapMarkerAlt);

// Fix for default markers in Leaflet
delete L.Icon.Default.prototype._getIconUrl;
L.Icon.Default.mergeOptions({
  iconRetinaUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-icon-2x.png',
  iconUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-icon.png',
  shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-shadow.png',
});

export default {
  name: 'MapPage',
  components: {
    CardEventMap,
    FontAwesomeIcon
  },
  data() {
    return {
      map: null,
      events: [],
      selectedEvent: null,
      loading: false,
      markers: [],
      error: null
    };
  },
  async mounted() {
    this.initMap();
    await this.fetchEventsWithLocation();
  },
  beforeUnmount() {
    if (this.map) {
      this.map.remove();
    }
  },
  methods: {
    initMap() {
      // Initialize map centered on a default location
      this.map = L.map('map').setView([40.7128, -74.0060], 10);

      // Add tile layer
      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
      }).addTo(this.map);
    },

    async fetchEventsWithLocation() {
      this.loading = true;
      this.error = null;

      try {
        console.log('Fetching events with location...');
        const response = await api.get('/events/with-location');
        console.log('Events with location response:', response.data.data);

        if (response.data.success) {
          this.events = response.data.data || [];
          console.log(`Found ${this.events.length} events with location data`);
          this.addMarkersToMap();
        } else {
          throw new Error(response.data.message || 'Failed to fetch events');
        }
      } catch (error) {
        console.error('Failed to fetch events with location:', error);
        this.error = error.response?.data?.message || error.message || 'Failed to load events';
        this.events = [];
      } finally {
        this.loading = false;
      }
    },

    addMarkersToMap() {
      // Clear existing markers
      this.markers.forEach(marker => this.map.removeLayer(marker));
      this.markers = [];

      if (this.events.length === 0) return;

      // Add markers for each event
      this.events.forEach(event => {
        const lat = parseFloat(event.latitude);
        const lng = parseFloat(event.longitude);

        if (!isNaN(lat) && !isNaN(lng)) {
          // Create custom marker icon based on event category
          const markerColor = this.getMarkerColor(event.category?.name);

          const customIcon = L.divIcon({
            className: 'custom-marker',
            html: `<div style="background-color: ${markerColor}; width: 20px; height: 20px; border-radius: 50%; border: 2px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"></div>`,
            iconSize: [20, 20],
            iconAnchor: [10, 10]
          });

          const marker = L.marker([lat, lng], { icon: customIcon })
            .addTo(this.map)
            .on('click', () => {
              this.selectedEvent = event;
              this.map.setView([lat, lng], 15);
            });

          // Add popup with event details
          const popupContent = `
            <div class="text-center p-2">
              <strong class="text-sm">${event.title}</strong><br>
              <small class="text-gray-600">${event.location}</small><br>
              <small class="text-blue-600">${this.formatEventTime(event.start_time)}</small>
            </div>
          `;

          marker.bindPopup(popupContent);
          this.markers.push(marker);
        }
      });

      // Fit map to show all markers
      if (this.markers.length > 0) {
        const group = new L.featureGroup(this.markers);
        this.map.fitBounds(group.getBounds().pad(0.1));
      }
    },

    getMarkerColor(categoryName) {
      const colors = {
        'Sports': '#f05a5a', // red
        'Music': '#f9b54a', // orange  
        'Food': '#50c878', // green
        'Tech': '#46cdff', // cyan
        'Art': '#a020f0', // purple
        'Business': '#4285f4', // blue
        'default': '#5b6fff' // primary blue
      };

      return colors[categoryName] || colors.default;
    },

    async refreshEvents() {
      await this.fetchEventsWithLocation();
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

    formatEventTime(dateString) {
      const date = new Date(dateString);
      return date.toLocaleString('en-US', {
        weekday: 'short',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      });
    },

    goToEventDetail(eventId) {
      this.$router.push(`/event/${eventId}`);
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

.bg-primary-light {
  background-color: rgb(var(--color-primary-light));
}

.text-text-main {
  color: rgb(var(--color-text-main));
}

.hover\:bg-primary-light:hover {
  background-color: rgb(var(--color-primary-light));
}

#map {
  z-index: 1;
}

/* Custom marker styles */
:deep(.custom-marker) {
  background: transparent !important;
  border: none !important;
}
</style>