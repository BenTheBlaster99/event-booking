<template>
  <component :is="currentSlide" @skip="skipToLogin" @next="nextSlide" />
</template>



<script>
import OnboardingSlide1 from './OnboardingSlide1.vue'
import OnboardingSlide2 from './OnboardingSlide2.vue'
import OnboardingSlide3 from './OnboardingSlide3.vue'


export default {
  components: { OnboardingSlide1, OnboardingSlide2, OnboardingSlide3 },
  data() {
    return {
      currentIndex: 0,
      slides: [OnboardingSlide1, OnboardingSlide2, OnboardingSlide3] // Use the imported components directly
    }
  },
  computed: {

    currentSlide() {
      return this.slides[this.currentIndex]
    }

  },
  methods: {
    nextSlide() {
      if (this.currentIndex < this.slides.length - 1) {
        this.currentIndex++
      } else {
        this.$router.push('/login')
      }
    },
    skipToLogin() {
      this.$router.push('/login')  // Parent handles navigation
    }
  }

}



</script>
