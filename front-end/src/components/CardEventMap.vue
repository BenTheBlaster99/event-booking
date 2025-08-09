<script>
    import { library } from '@fortawesome/fontawesome-svg-core';
import { faCalendarAlt, faLocationDot } from '@fortawesome/free-solid-svg-icons';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';

library.add(faCalendarAlt, faLocationDot);

export default {
    name: 'CardEventMap',
    components: {
        FontAwesomeIcon
    },
    props:{
         imageUrl: {
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
    },
    time: {
      type: String,
      required: true
        },
    },
}
</script>

<template>
    <div class="w-[280px] h-28 bg-white rounded-lg shadow-md overflow-hidden flex items-center p-3 flex-shrink-0 cursor-pointer">
    <!-- Image placeholder/thumbnail -->
    <div class="w-20 h-20 rounded-lg overflow-hidden flex-shrink-0 mr-3">
      <img :src="imageUrl" alt="Event Image" class="w-full h-full object-cover">
    </div>
    
    <!-- Event details -->
    <div class="flex flex-col flex-grow">
      <h3 class="text-sm font-semibold text-text-main mb-1 truncate">{{ title }}</h3>
      <div class="flex items-center text-xs text-gray-500 mb-1">
        <font-awesome-icon :icon="['fas', 'calendar-alt']" class="mr-1" />
        <span>{{ time }}</span>
      </div>
      <div class="flex items-center text-xs text-gray-500">
        <font-awesome-icon :icon="['fas', 'location-dot']" class="mr-1" />
        <span>{{ location }}</span>
      </div>
    </div>
  </div>

</template>