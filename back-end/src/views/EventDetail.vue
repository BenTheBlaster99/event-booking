<template>
    <div v-if="isLoading" class="flex items-center justify-center h-screen text-gray-500">
      <p>Loading event details...</p>
    </div>
    <div v-else-if="error" class="flex items-center justify-center h-screen text-red-500">
      <p>{{ error }}</p>
    </div>
    <div v-else-if="event" class="w-full max-w-[480px] mx-auto min-h-screen bg-gray-50 relative">
      <!-- Event Image -->
      <div class="w-[375px] h-[244px] overflow-hidden relative -top-[23px]">
        <img :src="event.imageUrl" alt="Event Cover" class="w-full h-full object-cover">
        
        <!-- Back button -->
        <button @click="goBack" class="absolute top-4 left-4 w-10 h-10 rounded-full bg-white/70 flex items-center justify-center backdrop-blur-sm">
          <font-awesome-icon :icon="['fas', 'arrow-left']" />
        </button>
      </div>
  
      <!-- Attendees Group -->
      <div class="absolute w-[295px] h-[60px] top-[191px] left-[40px] bg-white rounded-full shadow-md flex items-center justify-between px-4 z-10">
        <div class="flex items-center -space-x-3">
          <div v-for="(attendee, index) in attendees" :key="index"
            class="w-10 h-10 rounded-full bg-gray-300 border-2 border-white"></div>
          <div v-if="attendees.length > 0"
            class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center text-white text-xs border-2 border-white">
            +{{ attendees.length }}
          </div>
        </div>
        <button class="bg-blue-600 text-white text-sm py-2 px-4 rounded-full">
          Invite
        </button>
      </div>
  
      <!-- Event Title -->
      <div class="absolute w-[313px] h-[92px] top-[271px] left-[24px]">
        <h1 class="text-2xl font-bold text-text-main">{{ event.title }}</h1>
      </div>
  
      <!-- Date and Time -->
      <div class="absolute w-[205px] h-[53px] top-[381px] left-[21px] flex items-center space-x-3">
        <font-awesome-icon :icon="['fas', 'calendar-alt']" class="text-blue-600 text-lg" />
        <div>
          <p class="font-semibold">{{ event.date }}</p>
          <p class="text-sm text-gray-500">{{ event.day }}, {{ event.time }}</p>
        </div>
      </div>
  
      <!-- Location -->
      <div class="absolute w-[181px] h-[51px] top-[450px] left-[62px] flex items-center space-x-3">
        <font-awesome-icon :icon="['fas', 'map-marker-alt']" class="text-blue-600 text-lg" />
        <div>
          <p class="font-semibold">{{ event.venue }}</p>
          <p class="text-sm text-gray-500">{{ event.address }}</p>
        </div>
      </div>
  
      <!-- Organizer -->
      <div class="absolute w-[327px] h-[44px] top-[527px] left-[24px] flex items-center justify-between">
        <div class="flex items-center space-x-3">
          <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-500">
            <font-awesome-icon :icon="['fas', 'user']" />
          </div>
          <div>
            <p class="text-sm font-semibold text-text-main">{{ event.organizer }}</p>
            <p class="text-xs text-gray-500">Organizer</p>
          </div>
        </div>
        <button class="text-blue-600 text-sm font-medium">Follow</button>
      </div>
  
      <!-- About Event -->
      <div class="absolute w-[375px] h-[181px] top-[634px] left-0 px-6">
        <h3 class="font-bold text-lg mb-2">About Event</h3>
        <p class="text-gray-600 leading-relaxed whitespace-pre-wrap mb-4">{{ event.description }}</p>
      </div>
  
      <!-- Buy Ticket Button -->
      <div class="fixed bottom-0 left-0 right-0 max-w-[480px] mx-auto bg-white border-t border-gray-200 p-4 shadow-lg">
        <button 
          @click="handleBuy" 
          class="w-full bg-blue-600 text-white py-4 rounded-lg font-semibold flex items-center justify-center"
        >
          <font-awesome-icon :icon="['fas', 'ticket-alt']" class="mr-2" />
          BUY TICKET ${{ event.price }}
        </button>
      </div>
    </div>
    <div v-else class="flex items-center justify-center h-screen text-gray-500">
      <p>No event data available.</p>
    </div>
  </template>
  
  <script>
  import api from '@/api';
  
  export default {
      data() {
          return {
              event: null,
              attendees: Array(5).fill({}), // Mock attendees data
              isLoading: true,
              error: null
          };
      },
      async created() {
          await this.fetchEvent();
      },
      methods: {
          async fetchEvent() {
              try {
                  this.isLoading = true;
                  const eventId = this.$route.params.id;
                  const response = await api.get(`/events/${eventId}`);
                  const eventData = response.data.data;
  
                  if (eventData) {
                      this.event = {
                          ...eventData,
                          imageUrl: eventData.image || 'https://placehold.co/600x400/2C3E50/FFF?text=Event+Cover',
                          date: new Date(eventData.start_time).toLocaleDateString('en-US', {
                              day: 'numeric',
                              month: 'long',
                              year: 'numeric'
                          }),
                          day: new Date(eventData.start_time).toLocaleDateString('en-US', { weekday: 'long' }),
                          time: `${new Date(eventData.start_time).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })} - ${new Date(eventData.end_time).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}`,
                          venue: eventData.location,
                          address: eventData.location,
                          organizer: eventData.user ? eventData.user.name : 'Unknown Organizer',
                          description: eventData.description,
                          price: eventData.price || '120' // Default to $120 as in design
                      };
                  }
              } catch (error) {
                  this.error = 'Failed to load event details. Please try again.';
              } finally {
                  this.isLoading = false;
              }
          },
          async handleBuy() {
              // Show participation alert
              alert('You are now participating in this event! Redirecting you back...');
              
              // Wait 2 seconds then go back
              setTimeout(() => {
                  this.$router.push('/events');
              }, 2000);
          },
          goBack() {
              this.$router.go(-1);
          }
      }
  };
  </script>
  
  <style scoped>
  /* No additional styles needed - using absolute positioning with exact dimensions */
  </style>