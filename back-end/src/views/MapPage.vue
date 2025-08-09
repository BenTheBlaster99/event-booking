<template>
    <div class="w-full h-full bg-gray-50 flex flex-col">
      <!-- Header -->
      <header class="p-4 bg-white shadow-sm flex items-center justify-between relative z-10">
        <button @click="$router.go(-1)" class="text-xl">
          <font-awesome-icon :icon="['fas', 'arrow-left']" />
        </button>
        <h1 class="text-lg font-bold text-text-main">Map</h1>
        <button class="text-xl">
          <font-awesome-icon :icon="['fas', 'search']" />
        </button>
      </header>
  
      <!-- Map Container -->
      <div class="flex-1 relative">
        <div id="map" class="w-full h-full"></div>
        
        <!-- Event Card Overlay (shows when marker is clicked) -->
        <div v-if="selectedEvent" class="absolute bottom-4 left-4 right-4 z-20">
          <CardEventMap
            :imageUrl="getImageUrl(selectedEvent.image)"
            :title="selectedEvent.title"
            :location="selectedEvent.location"
            :time="formatEventTime(selectedEvent.start_time)"
            @click="goToEventDetail(selectedEvent.id)"
          />
        </div>
  
        <!-- Loading overlay -->
        <div v-if="loading" class="absolute inset-0 bg-white bg-opacity-75 flex items-center justify-center z-10">
          <div class="text-center">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary mx-auto mb-2"></div>
            <p class="text-gray-600">Loading events...</p>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  import L from 'leaflet';
  import api from '@/api';
  import CardEventMap from '@/components/CardEventMap.vue';
  import { library } from '@fortawesome/fontawesome-svg-core';
  import { faArrowLeft, faSearch } from '@fortawesome/free-solid-svg-icons';
  import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
  
  library.add(faArrowLeft, faSearch);
  
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
        markers: []
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
        // Initialize map centered on a default location (you can change this)
        this.map = L.map('map').setView([40.7128, -74.0060], 12); // New York City default
  
        // Add tile layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
          attribution: '© OpenStreetMap contributors'
        }).addTo(this.map);
      },
  
      async fetchEventsWithLocation() {
        this.loading = true;
        try {
          // Fetch events that have latitude and longitude
          const response = await api.get('/events/with-location');
          this.events = response.data.data || [];
          this.addMarkersToMap();
        } catch (error) {
          console.error('Failed to fetch events with location:', error);
          // Fallback to dummy data for demonstration
          this.events = [
            {
              id: 1,
              title: 'Music Festival',
              location: 'Central Park, NY',
              latitude: 40.7829,
              longitude: -73.9654,
              start_time: '2024-06-15T18:00:00Z',
              image: null
            },
            {
              id: 2,
              title: 'Art Exhibition',
              location: 'Brooklyn Museum',
              latitude: 40.6712,
              longitude: -73.9636,
              start_time: '2024-06-20T14:00:00Z',
              image: null
            }
          ];
          this.addMarkersToMap();
        } finally {
          this.loading = false;
        }
      },
  
      addMarkersToMap() {
        // Clear existing markers
        this.markers.forEach(marker => this.map.removeLayer(marker));
        this.markers = [];
  
        // Add markers for each event
        this.events.forEach(event => {
          if (event.latitude && event.longitude) {
            const marker = L.marker([event.latitude, event.longitude])
              .addTo(this.map)
              .on('click', () => {
                this.selectedEvent = event;
                this.map.setView([event.latitude, event.longitude], 15);
              });
  
            // Add popup with event title
            marker.bindPopup(`<strong>${event.title}</strong><br>${event.location}`);
            this.markers.push(marker);
          }
        });
  
        // Fit map to show all markers if there are any
        if (this.markers.length > 0) {
          const group = new L.featureGroup(this.markers);
          this.map.fitBounds(group.getBounds().pad(0.1));
        }
      },
  
      getImageUrl(image) {
        // Handle image URL - provide fallback if image is null/undefined
        if (!image) {
          return 'https://placehold.co/300x200/E0F2F7/2C3E50?text=Event';
        }
        // If image is a full URL, return as is
        if (image.startsWith('http')) {
          return image;
        }
        // If it's a relative path, prepend your backend URL
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
  
  .text-text-main {
    color: rgb(var(--color-text-main));
  }
  
  #map {
    z-index: 1;
  }
  </style>