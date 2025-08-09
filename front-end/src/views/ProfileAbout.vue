<script>
export default {
    name: 'ProfileAbout'
}
</script>

<template>
    <div class="p-4 bg-white">
        <h2 class="text-lg font-bold text-text-main mb-2">About</h2>
        <p class="text-gray-600 leading-relaxed mb-4">
            Enjoy your favorite dishes and a lovely time with your friends and family.
            Food from local food trucks will be available for breakfast or lunch.
            Lorem ipsum dolor sit amet, consectetur adipiscing elit.
            Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.
            Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
        </p>
        <button class="text-primary text-sm font-semibold">Read More</button>
    </div>
</template>

<style scoped>
.text-primary {
    color: rgb(var(--color-primary));
}

.text-text-main {
    color: rgb(var(--color-text-main));
}
</style>