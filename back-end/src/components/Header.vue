<template>
    <header class="absolute top-0 inset-x-0 z-40"
      style="width: 3; height: 179px; border-bottom-left-radius: 33px; border-bottom-right-radius: 33px; background-color: rgb(var(--color-primary)); overflow: hidden;">
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
        <div class="flex items-center bg-white rounded-full px-4 py-2 shadow-sm"
          style="width: 327px; height: 48px; margin-left: auto; margin-right: auto;">
          <font-awesome-icon :icon="['fas', 'search']" class="text-gray-500 mr-2" />
          <input type="text" placeholder="Search..."
            class="flex-1 bg-transparent outline-none text-sm text-gray-700" readonly>
          <button class="bg-gray-200 text-xs px-3 py-1 rounded-full text-gray-700 font-medium">
            <font-awesome-icon :icon="['fas', 'filter']" class="mr-1" />Filter
          </button>
        </div>
      </div>
  
      <!-- Category Buttons - Moved here and styled -->
      <div class="flex overflow-x-auto gap-2 px-4 pb-4 scrollbar-hide absolute bottom-0 left-0 right-0"
           style="width: 419px; height: 39.42px; transform: translateX(-24px);">
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
  