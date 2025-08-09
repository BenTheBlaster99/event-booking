<template>
  <OnboardingContainer image="/src/images/onboarding3.png" title="To Look Up More Events or Activities Nearby By Map"
    description="In publishing and graphic design, Lorem is a placeholder text commonly" @prev="handlePrev" />
</template>

<script>
import OnboardingContainer from '../components/OnboardingContainer.vue'
export default {
  components: { OnboardingContainer },
  methods: {
    handleSkip() {
      this.$router.push('/login')
    },
    handleNext() {
      this.$emit('next')
    },
    handlePrev() {
      this.$emit('prev')
    }
  }
}

</script>
