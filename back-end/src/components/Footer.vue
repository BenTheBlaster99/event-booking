<template>
  <footer class="absolute bottom-0 inset-x-0 bg-white border-t border-gray-200 py-2 px-4 shadow-md z-40"
    style="width: 375px; height: 111px; margin-left: auto; margin-right: auto;">
    <div class="flex justify-around max-w-[375px] mx-auto h-full items-center">
      <router-link to="/home" class="flex flex-col items-center text-xs transition-colors duration-200"
        :class="{ 'text-primary': $route.path === '/home', 'text-gray-500': $route.path !== '/home' }">
        <font-awesome-icon :icon="['fas', 'compass']" class="text-lg mb-0.5" />
        <span>Explore</span>
      </router-link>

      <router-link to="/home/events" class="flex flex-col items-center text-xs transition-colors duration-200"
        :class="{ 'text-primary': $route.path === '/home/events', 'text-gray-500': $route.path !== '/home/events' }">
        <font-awesome-icon :icon="['fas', 'calendar-alt']" class="text-lg mb-0.5" />
        <span>Events</span>
      </router-link>

      <router-link to="/home/create" class="flex flex-col items-center text-xs transition-colors duration-200"
        :class="{ 'text-primary': $route.path === '/home/create', 'text-gray-500': $route.path !== '/home/create' }">
        <div class="bg-primary text-white rounded-full w-10 h-10 flex items-center justify-center text-xl shadow-lg">
          <font-awesome-icon :icon="['fas', 'plus']" />
        </div>
        <span>Add</span>
      </router-link>

      <router-link to="/home/map" class="flex flex-col items-center text-xs transition-colors duration-200"
        :class="{ 'text-primary': $route.path === '/home/map', 'text-gray-500': $route.path !== '/home/map' }">
        <font-awesome-icon :icon="['fas', 'map-marker-alt']" class="text-lg mb-0.5" />
        <span>Map</span>
      </router-link>

      <router-link to="/home/profile" class="flex flex-col items-center text-xs transition-colors duration-200"
        :class="{ 'text-primary': $route.path.startsWith('/home/profile'), 'text-gray-500': !$route.path.startsWith('/home/profile') }">
        <font-awesome-icon :icon="['fas', 'user']" class="text-lg mb-0.5" />
        <span>Profile</span>
      </router-link>
    </div>
  </footer>
</template>

<script>
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { library } from '@fortawesome/fontawesome-svg-core';
import {
  faCompass,
  faCalendarAlt,
  faPlus,
  faMapMarkerAlt,
  faUser
} from '@fortawesome/free-solid-svg-icons';

// Add the icons to the library
library.add(faCompass, faCalendarAlt, faPlus, faMapMarkerAlt, faUser);

export default {
  components: {
    FontAwesomeIcon,
  },
};
</script>

<style scoped>
/* No specific scoped styles needed if using Tailwind and inline styles */
</style>
