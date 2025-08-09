<script>
import Header from './Header.vue';
import Footer from './Footer.vue'; // Assuming Footer.vue is in components

export default {
    components: { Header, Footer },
    data() {
        return {
            isMenuOpen: false,
            menuItems: [
                { name: 'My Profile', path: '/home/profile' }, // Adjusted path for nested routing
                { name: 'Message', path: '/home/messages' }, // Adjusted path for nested routing
                { name: 'Calendar', path: '/home/calendar' }, // Adjusted path for nested routing
                { name: 'Bookmark', path: '/home/bookmarks' }, // Adjusted path for nested routing
                { name: 'Contact Us', path: '/home/contact' }, // Adjusted path for nested routing
                { name: 'Settings', path: '/home/settings' }, // Adjusted path for nested routing
                { name: 'Helps & FAQs', path: '/home/help' }, // Adjusted path for nested routing
                { name: 'Sign Out', path: '/logout' } // This can redirect to /signin
            ]
        }
    },
    computed: {
        isHomePage() {
            return this.$route.path === '/home'
        }
    },
    methods: {
        // Method to handle sign out, can be expanded later
        signOut() {
            console.log('Signing out...');
            this.$router.push('/signin');
        }
    }
}
</script>


<template>
    <!-- This div now fills the App.vue container and provides padding for header/footer -->
    <div class="relative w-full h-full pb-[60px] pt-[60px] overflow-auto">
        <!-- Header component -->
        <Header @toggleMenu="isMenuOpen = !isMenuOpen" v-if="isHomePage" />


        <main class="pb-4 h-full">
            <router-view /> <!-- This is where MainPage or SearchPage will render -->
        </main>


        <Footer v-if="isHomePage" />


        <div v-if="isMenuOpen" class="fixed inset-0 z-40" @click="isMenuOpen = false"></div>

        <!-- Side Menu Panel (the actual menu content) -->
        <div class="absolute top-0 bottom-0 left-0 bg-white w-[280px] shadow-lg transform transition-transform duration-300 z-50"
            :class="{ '-translate-x-full': !isMenuOpen, 'translate-x-0': isMenuOpen }">
            <div class="bg-primary p-6 text-white">
                <div class="w-16 h-16 rounded-full bg-white/20 flex items-center justify-center text-2xl mb-2">
                    👤
                </div>
                <h2 class="text-xl font-bold">Ashfak Sayem</h2>
            </div>
            <nav class="p-4">
                <router-link v-for="item in menuItems" :key="item.name" :to="item.path"
                    class="block py-3 border-b border-gray-100" @click="isMenuOpen = false">
                    {{ item.name }}
                </router-link>
            </nav>
        </div>
    </div>
</template>
