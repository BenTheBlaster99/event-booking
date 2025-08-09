<template>
  <div v-if="isLoading" class="flex items-center justify-center h-screen text-gray-500">
    <p>Loading event details...</p>
  </div>
  <div v-else-if="error" class="flex items-center justify-center h-screen text-red-500">
    <p>{{ error }}</p>
  </div>
  <div v-else-if="event" class="w-full max-w-[480px] mx-auto min-h-screen bg-gray-50 relative">
    <!-- Event Image Container -->
    <div class="w-full h-[244px] overflow-hidden relative">
      <img :src="event.imageUrl" alt="Event Cover" class="w-full h-full object-cover">

      <!-- Back button -->
      <button @click="goBack" class="absolute top-4 left-4 w-10 h-10 rounded-full bg-white/70 flex items-center justify-center backdrop-blur-sm">
        <font-awesome-icon :icon="['fas', 'arrow-left']" />
      </button>
      
      <!-- Bookmark button -->
      <button class="absolute top-4 right-4 w-10 h-10 rounded-full bg-white/70 flex items-center justify-center backdrop-blur-sm text-gray-800">
        <font-awesome-icon :icon="['fas', 'bookmark']" />
      </button>
    </div>

    <!-- Attendees Group -->
    <div class="absolute w-[295px] h-[60px] -mt-10 left-[40px] right-[40px] bg-white rounded-full shadow-md flex items-center justify-between px-4 z-10 mx-auto">
      <div class="flex items-center -space-x-3">
        <!-- Mock attendees, using a consistent avatar placeholder -->
        <img v-for="(attendee, index) in attendees" :key="index"
          :src="`https://i.pravatar.cc/150?img=${index}`"
          class="w-10 h-10 rounded-full border-2 border-white"
          :class="{'ml-3': index === 0}">
        <div v-if="attendees.length > 0"
          class="w-10 h-10 rounded-full flex items-center justify-center text-white text-xs border-2 border-white"
          style="background-color: rgb(var(--color-primary));">
          +{{ attendees.length }}
        </div>
      </div>
      <button class="text-white text-sm py-2 px-4 rounded-full"
        style="background-color: rgb(var(--color-primary));">
        Invite
      </button>
    </div>

    <!-- Event Content Section -->
    <div class="p-6 pt-16 flex flex-col space-y-6">
      <!-- Event Title -->
      <div>
        <h1 class="text-2xl font-bold text-gray-800 leading-tight">{{ event.title }}</h1>
      </div>

      <!-- Date and Time -->
      <div class="flex items-center space-x-4">
        <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background-color: rgba(var(--color-primary), 0.1);">
          <font-awesome-icon :icon="['fas', 'calendar-alt']" class="text-lg" style="color: rgb(var(--color-primary));"/>
        </div>
        <div>
          <p class="font-semibold text-gray-800">{{ event.date }}</p>
          <p class="text-sm text-gray-500">{{ event.day }}, {{ event.time }}</p>
        </div>
      </div>

      <!-- Location -->
      <div class="flex items-center space-x-4">
        <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background-color: rgba(var(--color-primary), 0.1);">
          <font-awesome-icon :icon="['fas', 'map-marker-alt']" class="text-lg" style="color: rgb(var(--color-primary));" />
        </div>
        <div>
          <p class="font-semibold text-gray-800">{{ event.venue }}</p>
          <p class="text-sm text-gray-500">{{ event.address }}</p>
        </div>
      </div>

      <!-- Organizer -->
      <div class="flex items-center justify-between py-2">
        <div class="flex items-center space-x-3">
          <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-500">
            <font-awesome-icon :icon="['fas', 'user']" />
          </div>
          <div>
            <p class="text-sm font-semibold text-gray-800">{{ event.organizer }}</p>
            <p class="text-xs text-gray-500">Organizer</p>
          </div>
        </div>
        <button class="text-sm font-medium" style="color: rgb(var(--color-primary));">Follow</button>
      </div>

      <!-- About Event -->
      <div>
        <h3 class="font-bold text-lg mb-2 text-gray-800">About Event</h3>
        <p class="text-gray-600 leading-relaxed whitespace-pre-wrap mb-4">{{ event.description }}</p>
      </div>
    </div>

    <!-- Buy Ticket Button -->
    <div class="fixed bottom-0 left-0 right-0 max-w-[480px] mx-auto bg-white border-t border-gray-200 p-4 shadow-lg z-20 flex justify-center items-center"
      style="width: 375px; height: 110px;">
      <button @click="handleBuy"
        class="text-white py-4 rounded-lg font-semibold flex items-center justify-center w-[271px] h-[58px]"
        style="background-color: rgb(var(--color-primary));">
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
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { library } from '@fortawesome/fontawesome-svg-core';
import {
  faArrowLeft,
  faBookmark,
  faCalendarAlt,
  faMapMarkerAlt,
  faUser,
  faTicketAlt
} from '@fortawesome/free-solid-svg-icons';

library.add(faArrowLeft, faBookmark, faCalendarAlt, faMapMarkerAlt, faUser, faTicketAlt);

export default {
    components: {
      FontAwesomeIcon,
    },
    data() {
        return {
            event: null,
            // Updated mock attendees to use an array of objects for image placeholders
            attendees: Array(3).fill({}),
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
                        imageUrl: eventData.image_url ? `http://localhost:8000/storage/${eventData.image_url}` : 'https://placehold.co/600x400/2C3E50/FFF?text=Event+Cover',
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
