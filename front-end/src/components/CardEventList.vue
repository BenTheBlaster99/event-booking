<script>
    import { library } from '@fortawesome/fontawesome-svg-core';
import { faMapMarkerAlt, faBookmark } from '@fortawesome/free-solid-svg-icons';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
library.add(faMapMarkerAlt, faBookmark);

export default {
    name: 'CardEventList',
    components: {
        FontAwesomeIcon
    },
    props:{
        eventID: {
      type: [String, Number],
      required: true
    },
    imageUrl: {
      type: String,
      required: true
    },
    dateDay: {
      type: [String, Number],
      required: true
    },
    dateMonth: {
      type: String,
      required: true
    },
    title: {
      type: String,
      required: true
    },
    location: {
      type: String,
      required: true
    }
    },
    computed:{
        dateFormatted(){
            const monthMap ={ 
                'JAN': 'Jan', 'FEB': 'Feb', 'MAR': 'Mar', 'APR': 'Apr', 'MAY': 'May', 'JUN': 'Jun',
        'JUL': 'Jul', 'AUG': 'Aug', 'SEP': 'Sep', 'OCT': 'Oct', 'NOV': 'Nov', 'DEC': 'Dec'
            };
            const dayOfWeek = new Date().toLocaleString('en-US', {weekday: 'short'});
            const month = monthMap[this.dateMonth.toUpperCase()] || this.dateMonth;
            return `${dayOfWeek}, ${month} ${this.dateDay} - ${this.time}`;
        }
    }
}
</script>

<template>
  <!-- Main card container for list view -->
  <router-link 
    :to="`/event/${eventID}`" 
    class="flex items-center bg-white rounded-xl shadow-sm relative overflow-hidden p-2 w-full"
    style="height: 112px;"
  >
    <!-- Image container -->
    <div class="w-[79px] h-[92px] rounded-lg overflow-hidden flex-shrink-0 mr-4">
      <img :src="imageUrl" alt="Event Image" class="w-full h-full object-cover">
    </div>

    <!-- Event Details -->
    <div class="flex-1 flex flex-col justify-center">
      <p class="text-xs text-primary font-medium mb-1">{{ dateFormatted }}</p>
      <h3 class="text-base font-semibold text-text-main leading-tight">{{ title }}</h3>
      <div class="flex items-center text-gray-600 text-sm mt-1">
        <font-awesome-icon :icon="['fas', 'map-marker-alt']" class="mr-1 text-xs" />
        <span>{{ location }}</span>
      </div>
    </div>

    <!-- Bookmark Icon -->
    <button class="absolute top-3 right-3 text-gray-400">
      <font-awesome-icon :icon="['fas', 'bookmark']" />
    </button>
  </router-link>
</template>