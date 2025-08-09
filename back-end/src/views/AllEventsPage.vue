<script>
import CardEvent from '@/components/CardEvent.vue';
import { library } from '@fortawesome/fontawesome-svg-core';
import { faArrowLeft, faSearch } from '@fortawesome/free-solid-svg-icons';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';

library.add(faArrowLeft, faSearch);

export default {
  name: 'AllEventsPage',
  components: {
    CardEvent,
    FontAwesomeIcon
  },
  data() {
    return {
      allEvents: [
        // Dummy data, 
        {
          eventID: '1',
          imageUrl: 'https://placehold.co/300x200/E0F2F7/2C3E50?text=Event+1',
          dateDay: '10',
          dateMonth: 'JUNE',
          title: 'International Band Music Concert',
          location: '36 Guild Street London, UK',
          time: '10:00 AM'
        },
        {
          eventID: '2',
          imageUrl: 'https://placehold.co/300x200/F7E0E0/502C2C?text=Event+2',
          dateDay: '15',
          dateMonth: 'JULY',
          title: 'Jo Malone London\'s Mother\'s Day',
          location: 'Radius Gallery, Santa Cruz',
          time: '5:30 PM'
        },
        {
          eventID: '3',
          imageUrl: 'https://placehold.co/300x200/E0F7E0/2C502C?text=Event+3',
          dateDay: '20',
          dateMonth: 'AUG',
          title: 'A virtual evening of jazz',
          location: 'Online Event',
          time: '07:00 PM'
        },
        {
          eventID: '4',
          imageUrl: 'https://placehold.co/300x200/F7F0E0/504A2C?text=Event+4',
          dateDay: '05',
          dateMonth: 'SEP',
          title: 'Women\'s Leadership Conference',
          location: 'Conference Center, NY',
          time: '09:00 AM'
        },
        {
          eventID: '5',
          imageUrl: 'https://placehold.co/300x200/E0E0F7/2C2C50?text=Event+5',
          dateDay: '12',
          dateMonth: 'OCT',
          title: 'International Kids Safe Parents Night Out',
          location: 'Community Hall',
          time: '06:00 PM'
        },
      ]
    };
  },
  methods: {
    // Methods to fetch all events from backend 
  }
};
</script>

<template>
  <div class="w-full h-full bg-gray-50 flex flex-col">
    <!-- Header -->
    <header class="p-4 bg-white shadow-sm flex items-center justify-between">
      <button @click="$router.go(-1)" class="text-xl mr-4">
        <font-awesome-icon :icon="['fas', 'arrow-left']" />
      </button>
      <h1 class="text-lg font-bold text-text-main flex-1">All Events</h1>
      <button class="text-xl">
        <font-awesome-icon :icon="['fas', 'search']" />
      </button>
    </header>

    <!-- Events List -->
     <div class="flex-1 p-4 overflow-y-auto space-y-4">
      <!-- Using the new CardEventList component -->
      <CardEventList
        v-for="event in allEvents"
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

.scrollbar-hide {
  -ms-overflow-style: none; 
  scrollbar-width: none; 
}
.scrollbar-hide::-webkit-scrollbar {
  display: none; 
}
</style>