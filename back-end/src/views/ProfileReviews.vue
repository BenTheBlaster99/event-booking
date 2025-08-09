<script>
export default {
    name: 'ProfileReviews',
    data() {
        return {
            reviews: [
                // dummy data as always
                {
                    reviewerName: 'Rocks Velkeinjen',
                    avatarUrl: 'https://placehold.co/40x40/E0E0E0/808080?text=👤',
                    rating: 5,
                    comment: 'Cinemas is the ultimate experience to see new movies in Gold Class or Vmax. Find a cinema near you.',
                    date: '10 Feb'
                },
                {
                    reviewerName: 'Angelina Zolly',
                    avatarUrl: 'https://placehold.co/40x40/E0E0E0/808080?text=👤',
                    rating: 4,
                    comment: 'Cinemas is the ultimate experience to see new movies in Gold Class or Vmax. Find a cinema near you.',
                    date: '10 Feb'
                },
                {
                    reviewerName: 'Zenifero Bolex',
                    avatarUrl: 'https://placehold.co/40x40/E0E0E0/808080?text=👤',
                    rating: 5,
                    comment: 'Cinemas is the ultimate experience to see new movies in Gold Class or Vmax. Find a cinema near you.',
                    date: '10 Feb'
                },
            ]
        };
    }
}
</script>

<template>
    <div class="p-4 bg-gray-50 space-y-4">
    <!-- Individual Review Card -->
    <div 
      v-for="(review, index) in reviews" 
      :key="index"
      class="bg-white rounded-xl shadow-sm p-4 flex items-start"
    >
      <img :src="review.avatarUrl" alt="Reviewer Avatar" class="w-10 h-10 rounded-full object-cover mr-4 flex-shrink-0">
      <div class="flex-1">
        <div class="flex justify-between items-center mb-1">
          <h3 class="font-semibold text-text-main">{{ review.reviewerName }}</h3>
          <span class="text-gray-500 text-xs">{{ review.date }}</span>
        </div>
        <div class="flex items-center mb-2">
          <!-- Star Rating (using emojis for now) -->
          <span v-for="n in 5" :key="n" class="text-yellow-400 text-sm">
            {{ n <= review.rating ? '⭐' : '☆' }}
          </span>
        </div>
        <p class="text-gray-600 text-sm leading-snug">{{ review.comment }}</p>
      </div>
    </div>
    <p v-if="reviews.length === 0" class="text-center text-gray-600">No reviews yet.</p>
  </div>

</template>