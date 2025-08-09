<template>

  <div class="onboarding-screen w-full h-full relative overflow-hidden bg-white font-sans">

    <!-- Image Section -->

    <div class="onboarding-image-container absolute top-[63px] left-1/2 -translate-x-1/2"
      style="width: 375px; height: 475px;">

      <img :src="image" alt="Onboarding background" class="w-full h-full object-cover" />

    </div>



    <!-- Bottom container with details and buttons -->

    <div class="absolute bottom-0 left-1/2 -translate-x-1/2 bg-white p-6 pt-12 rounded-t-[48px] shadow-lg"
      style="width: 375px; height: 288px; top: 524px;">

      <div class="text-text-main">

        <h2 class="text-xl font-bold mb-2">{{ title }}</h2>

        <p class="text-sm text-text-gray1 leading-relaxed mb-6">{{ description }}</p>

        <div class="flex justify-between items-center mt-auto">

          <button v-if="showSkip" @click="$emit('skip')" class="text-primary font-semibold text-sm">Skip</button>

          <div v-else></div>

          <button @click="$emit('next')"
            class="bg-primary text-white w-10 h-10 rounded-full flex items-center justify-center transition-colors hover:bg-primary-light">

            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">

              <path fill-rule="evenodd"
                d="M7.293 14.707a1 1 0 010-1.414L10.586 10l-3.293-3.293a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                clip-rule="evenodd" />

            </svg>

          </button>

        </div>

      </div>

    </div>

  </div>

</template>



<script>

export default {

  props: {

    image: {

      type: String,

      required: true

    },

    title: {

      type: String,

      required: true

    },

    description: {

      type: String,

      required: true

    },

    showSkip: {

      type: Boolean,

      default: true

    }

  }

}

</script>
