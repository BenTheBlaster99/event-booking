<template>
    <div class="space-y-4">
        <div v-if="loading" class="text-center py-8">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary mx-auto mb-2"></div>
            <p class="text-gray-600">Loading your events...</p>
        </div>
        
        <div v-else-if="profileEvents.length === 0" class="text-center py-8 bg-white rounded-lg shadow-sm">
            <font-awesome-icon :icon="['fas', 'calendar-plus']" class="text-4xl text-gray-300 mb-4" />
            <h3 class="text-lg font-semibold text-gray-600 mb-2">No Events Created</h3>
            <p class="text-gray-500 mb-4">You haven't created any events yet.</p>
            <router-link 
                to="/home/create-event" 
                class="bg-primary text-white px-4 py-2 rounded-lg font-semibold hover:bg-primary-dark transition-colors inline-block"
            >
                Create Your First Event
            </router-link>
        </div>
        
        <div v-else>
            <CardEventList 
                v-for="event in profileEvents"
                :key="event.eventID"
                :eventID="event.eventID"
                :imageUrl="event.imageUrl"
                :dateDay="event.dateDay"
                :dateMonth="event.dateMonth"
                :title="event.title"
                :location="event.location"
                :time="event.time"
            />
        </div>
    </div>
</template>

<script>
import CardEventList from '@/components/CardEventList.vue';
import api from '@/api';
import { library } from '@fortawesome/fontawesome-svg-core';
import { faCalendarPlus } from '@fortawesome/free-solid-svg-icons';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';

library.add(faCalendarPlus);

export default {
    name: 'ProfileEvents',
    components: {
        CardEventList,
        FontAwesomeIcon
    },
    data() {
        return {
            profileEvents: [],
            loading: false
        };
    },
    async created() {
        await this.fetchUserEvents();
    },
    methods: {
        async fetchUserEvents() {
            this.loading = true;
            try {
                const response = await api.get('/user/events');
                console.log('User events response:', response.data);
                
                if (response.data.success) {
                    this.profileEvents = response.data.data.map(event => ({
                        eventID: event.id,
                        imageUrl: event.image ? `http://localhost:8000/storage/${event.image}` : 'https://placehold.co/300x200/E0F2F7/2C3E50?text=Event',
                        dateDay: new Date(event.start_time).getDate(),
                        dateMonth: new Date(event.start_time).toLocaleString('default', { month: 'short' }).toUpperCase(),
                        title: event.title,
                        location: event.location,
                        time: new Date(event.start_time).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
                    }));
                }
            } catch (error) {
                console.error('Failed to fetch user events:', error);
                this.profileEvents = [];
            } finally {
                this.loading = false;
            }
        }
    }
}
</script>

<style scoped>
.text-primary {
  color: rgb(var(--color-primary));
}

.bg-primary {
  background-color: rgb(var(--color-primary));
}

.bg-primary-dark {
  background-color: rgb(var(--color-primary-dark));
}

.hover\:bg-primary-dark:hover {
  background-color: rgb(var(--color-primary-dark));
}
</style>