<template>
  <header class="absolute top-0 inset-x-0 z-40"
    style="width: 375px; height: 160px; border-bottom-left-radius: 33px; border-bottom-right-radius: 33px; background-color: rgb(var(--color-primary));">
    <div class="flex justify-between items-center p-4 max-w-[375px] mx-auto text-white">
      <!-- Menu Button -->
      <button class="text-xl" @click="$emit('toggleMenu')">
        <font-awesome-icon :icon="['fas', 'bars']" />
      </button>

      <!-- Location Selector -->
      <div class="flex items-center gap-1 text-sm font-medium">
        <span>Current Location</span>
        <font-awesome-icon :icon="['fas', 'chevron-down']" class="text-[10px]" />
      </div>

      <!-- Notifications Icon -->
      <router-link to="/notifications" class="text-xl">
        <font-awesome-icon :icon="['fas', 'bell']" />
      </router-link>
    </div>

    <!-- Search Bar and Filter Button -->
    <div class="p-4 pt-0">
      <div @click="goToSearch"
        class="flex items-center text-white rounded-full px-4 py-2 shadow-sm"
        style="width: 327px; height: 48px; margin-left: auto; margin-right: auto; background-color: rgba(var(--color-primary), 0.2); border: 1px solid rgba(var(--color-primary), 0.3);">
        <font-awesome-icon :icon="['fas', 'search']" class="text-white mr-2" />
        <input type="text" placeholder="Search..."
          class="flex-1 bg-transparent outline-none text-sm text-white placeholder-white" readonly>
        <button @click.stop="$emit('openFilter')"
          class="text-white text-xs px-3 py-1 rounded-full font-medium"
          style="background-color: rgba(var(--color-primary), 0.3); border: 1px solid rgba(var(--color-primary), 0.4);">
          <font-awesome-icon :icon="['fas', 'filter']" class="mr-1" />Filter
        </button>
      </div>
    </div>

    <!-- Category Buttons - Moved here and styled -->
    <div class="flex gap-2 px-11 absolute scrollbar-hide left-0 right-0"
style="width: 419px; height: 39.42px; transform: translateX(-24px); bottom: -20px; overflow-x: auto; overflow-y: hidden;">
      <!-- Using hardcoded categories and colors for demonstration as per the image -->
      <button class="flex-shrink-0 px-4 py-2 rounded-full text-sm font-medium text-white flex items-center justify-center"
              style="width: 106.77px; height: 39.06px; background-color: rgb(var(--color-accent-red));">
        <font-awesome-icon :icon="['fas', 'basketball-ball']" class="mr-1" />Sports
      </button>
      <button class="flex-shrink-0 px-4 py-2 rounded-full text-sm font-medium text-white flex items-center justify-center"
              style="width: 106.77px; height: 39.06px; background-color: rgb(var(--color-accent-orange));">
        <font-awesome-icon :icon="['fas', 'music']" class="mr-1" />Music
      </button>
      <button class="flex-shrink-0 px-4 py-2 rounded-full text-sm font-medium text-white flex items-center justify-center"
              style="width: 106.77px; height: 39.06px; background-color: rgb(var(--color-accent-green));">
        <font-awesome-icon :icon="['fas', 'utensils']" class="mr-1" />Food
      </button>
      <button class="flex-shrink-0 px-4 py-2 rounded-full text-sm font-medium text-white flex items-center justify-center"
              style="width: 106.77px; height: 39.06px; background-color: rgb(var(--color-accent-darkCyan));">
        <font-awesome-icon :icon="['fas', 'laptop-code']" class="mr-1" />Tech
      </button>
      <button class="flex-shrink-0 px-4 py-2 rounded-full text-sm font-medium text-white flex items-center justify-center"
              style="width: 106.77px; height: 39.06px; background-color: rgb(var(--color-accent-purple));">
        <font-awesome-icon :icon="['fas', 'palette']" class="mr-1" />Art
      </button>
    </div>
  </header>
</template>

<script>
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { library } from '@fortawesome/fontawesome-svg-core';
import {
  faBars,
  faBell,
  faSearch,
  faFilter,
  faChevronDown,
  faBasketballBall,
  faMusic,
  faUtensils,
  faLaptopCode, // Added for Tech
  faPalette     // Added for Art
} from '@fortawesome/free-solid-svg-icons';

// Add the icons to the library
library.add(
  faBars,
  faBell,
  faSearch,
  faFilter,
  faChevronDown,
  faBasketballBall,
  faMusic,
  faUtensils,
  faLaptopCode,
  faPalette
);

export default {
  components: {
    FontAwesomeIcon,
  },
  emits: ['toggleMenu'],
  methods:{
    goToSearch(){
      this.$router.push('/home/search');
    },
  }
};
</script>

<style scoped>
.scrollbar-hide::-webkit-scrollbar {
  display: none;
}

.scrollbar-hide {
  -ms-overflow-style: none;
  /* IE and Edge */
  scrollbar-width: none;
  /* Firefox */
}
</style>