<template>
  
  <router-link :to="`/event/${eventID}`" class="card w-[237px] h-[255px] relative overflow-hidden flex-shrink-0">
    
    <!-- Image container with specified width, height, and absolute positioning -->
    <div class="absolute top-[9px] left-[9px] w-[218px] h-[131px] rounded-xl overflow-hidden">
      <img :src="imageUrl" alt="Event Image" class="w-full h-full object-cover">
    </div>

    <div class="absolute top-[18px] left-[18px] w-[45px] h-[46.68px] rounded-xl bg-secondary-lighter shadow-sm flex flex-col items-center justify-center text-center"> 
      <span class="block text-accent-red font-semibold text-base leading-none">{{ dateDay }}</span>
      <span class="block text-accent-red text-xs uppercase leading-none">{{ dateMonth }}</span>
    </div>

  
    <div class="absolute top-[161px] left-[19px] w-[198px] h-[84px] p-2 flex flex-col justify-start">
      <h3 class="card-title text-base font-semibold text-text-main leading-tight mb-1">{{ title }}</h3>
      
      <!-- Placeholder for people going - adjust as per your design -->
      <div class="flex items-center text-gray-600 text-sm mb-1">
        <span class="w-5 h-5 rounded-full bg-gray-200 flex items-center justify-center text-xs -mr-1">👤</span>
        <span class="w-5 h-5 rounded-full bg-gray-200 flex items-center justify-center text-xs">👤</span>
        <span class="ml-1 text-xs">+20 Going</span>
      </div>

      <!-- Location -->
      <div class="flex items-center text-gray-600 text-sm">
        <span class="mr-1">📍</span>
        <span>{{ location }}</span>
      </div>
    </div>
  </router-link>
</template>

<script>
export default {
    props: {
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
        },
        time: {
            type: String,
            required: false
        },
        eventID: {
            type: [String, Number],
            required: true
        }
    }
}
</script>

<style scoped>
/* Ensure custom colors are recognized if not globally available through Tailwind's JIT */
.text-accent-red {
    color: rgb(var(--color-accent-red));
}

.bg-secondary-lighter {
    background-color: rgb(var(--color-background-light)); /* Using your custom background-light as per previous instruction */
}
</style>
