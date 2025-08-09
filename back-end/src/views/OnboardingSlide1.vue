<template>
  <OnboardingContainer image="/src/images/onboarding1.png" title="Explore Upcoming and Nearby Events"
    description="In publishing and graphic design, Lorem is a placeholder text commonly" @skip="handleSkip"
    @next="handleNext" />
</template>

<script>
import OnboardingContainer from '../components/OnboardingContainer.vue'

export default {
  components: { OnboardingContainer },
  methods: {
    handleSkip() {
      this.$emit('skip')  // Emit to parent
    },
    handleNext() {
      this.$emit('next')
    }
  }
}
</script>