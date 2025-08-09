<script>
import { library } from '@fortawesome/fontawesome-svg-core';
import { faCalendarAlt, faChevronRight, faLocationDot } from '@fortawesome/free-solid-svg-icons';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';

library.add(faCalendarAlt, faChevronRight, faLocationDot);

export default {
    name: 'FilterModal',
    components: {
        FontAwesomeIcon
    },
    data() {
        return {
            categories: [
                //dummy data for categories. FETCH THEM FROM THE BACKEND
                { name: 'Sports', icon: '🏀' },
                { name: 'Music', icon: '🎵' },
                { name: 'Art', icon: '🎨' },
                { name: 'Food', icon: '🍔' },
                { name: 'Business', icon: '💼' },
                { name: 'Technology', icon: '💻' }
            ],
            selectedCategories: [],
            timeOptions: ['Today', 'Tomorrow', 'This week'],
            selectedTime: '',
            location: 'New York, USA',
            mixPrice: 20,
            maxPrice: 120,
            priceRange: [20, 120],
            isDragging: null,
            showCalendar: false,
        }
    },
    methods: {
        resetFilters() {
            this.selectedCategories = [];
            this.selectedTime = '';
            this.location = 'New York, USA';
            this.priceRange = [this.minPrice, this.maxPrice];
        },
        applyFilters() {
            const filters = {
                categories: this.selectedCategories,
                time: this.selectedTime,
                location: this.location,
                priceRange: this.priceRange
            };
            this.$emit('apply', filters);
            this.$emit('close');
        },
        startDrag(handle) {
            this.isDragging = handle;
            window.addEventListener('mousemove', this.onDrag);
            window.addEventListener('mouseup', this.stopDrag);
        },
        onDrag(e) {
            if (!this.isDragging) return;
            const slider = this.$el.querySelector('.relative.w-fill.h-1');
            if (!slider) return;

            const rect = slider.getBoundingClientRect();
            let newPosition = (e.clientX - rect.left) / rect.windth;

            newPosition = Math.max(0, Math.min(newPosition, 1));

            const newPrice = Math.round(this.minPrice + newPosition * (this.maxPrice - this.minPrice));

            if (this.isDragging === 'min') {
                if (newPrice < this.priceRange[1]) {
                    this.priceRange[0] = newPrice;
                }
            } else if (this.isDragging === 'max') {
                if (newPrice > this.priceRange[0]) {
                    this.priceRange[1] = newPrice;
                }
            }
        },
        stopDrag() {
            this.isDragging = null;
            window.removeEventListener('mousemove', this.onDrag);
            window.removeEventListener('mouseup', this.stopDrag);
        }
    }
}
</script>

<template>
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay, show/hide based on modal state. -->
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"
                @click="$emit('close')"></div>

            <!-- This element is to trick the browser into centering the modal contents. -->
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <!-- Modal panel, show/hide based on modal state. -->
            <div
                class="inline-block align-bottom bg-white rounded-t-3xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle w-full">
                <div class="p-6">
                    <!-- Drag handle -->
                    <div class="flex justify-center mb-4">
                        <div class="w-12 h-1 bg-gray-300 rounded-full"></div>
                    </div>

                    <h3 class="text-xl leading-6 font-bold text-text-main mb-6" id="modal-title">Filter</h3>

                    <!-- Categories Section -->
                    <div class="mb-6">
                        <h4 class="text-lg font-semibold text-text-main mb-4">Categories</h4>
                        <div class="flex overflow-x-auto space-x-4 pb-2 scrollbar-hide">
                            <button v-for="category in categories" :key="category.name"
                                class="flex-shrink-0 flex flex-col items-center p-2 rounded-xl transition-colors duration-200"
                                :class="{
                                    'bg-primary text-white': selectedCategories.includes(category.name),
                                    'bg-gray-100 text-gray-700': !selectedCategories.includes(category.name)
                                }" @click="toggleCategory(category.name)">
                                <div class="w-12 h-12 flex items-center justify-center rounded-full mb-1"
                                    :class="{ 'bg-white/20': selectedCategories.includes(category.name), 'bg-white': !selectedCategories.includes(category.name) }">
                                    <span class="text-2xl">{{ category.icon }}</span>
                                </div>
                                <span class="text-xs font-medium">{{ category.name }}</span>
                            </button>
                        </div>
                    </div>

                    <!-- Time & Date Section -->
                    <div class="mb-6">
                        <h4 class="text-lg font-semibold text-text-main mb-4">Time & Date</h4>
                        <div class="flex space-x-2 mb-4">
                            <button v-for="time in timeOptions" :key="time"
                                class="flex-1 py-2 px-4 rounded-lg font-semibold transition-colors duration-200" :class="{
                                    'bg-primary text-white': selectedTime === time,
                                    'bg-gray-100 text-gray-700': selectedTime !== time
                                }" @click="selectedTime = time">
                                {{ time }}
                            </button>
                        </div>
                        <button
                            class="w-full flex items-center justify-between py-3 px-4 rounded-lg bg-gray-100 text-gray-700 font-semibold"
                            @click="showCalendar = true">
                            <div class="flex items-center">
                                <font-awesome-icon :icon="['fas', 'calendar-alt']" class="mr-2 text-primary" />
                                <span>Choose from calendar</span>
                            </div>
                            <font-awesome-icon :icon="['fas', 'chevron-right']" class="text-gray-500" />
                        </button>
                    </div>

                    <!-- Location Section -->
                    <div class="mb-6">
                        <h4 class="text-lg font-semibold text-text-main mb-4">Location</h4>
                        <div class="relative">
                            <font-awesome-icon :icon="['fas', 'location-dot']"
                                class="absolute left-4 top-1/2 -translate-y-1/2 text-primary" />
                            <input type="text" placeholder="New York, USA"
                                class="w-full pl-10 pr-4 py-3 rounded-lg bg-gray-100 text-gray-700 font-semibold focus:outline-none focus:ring-2 focus:ring-primary"
                                v-model="location">
                            <font-awesome-icon :icon="['fas', 'chevron-right']"
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500" />
                        </div>
                    </div>

                    <!-- Price Range Section -->
                    <div class="mb-8">
                        <h4 class="text-lg font-semibold text-text-main mb-4">Select price range</h4>
                        <div class="flex justify-between items-center mb-2">
                            <span class="font-semibold text-sm text-gray-700">${{ minPrice }} - ${{ maxPrice }}</span>
                            <span class="font-semibold text-sm text-gray-700">${{ priceRange[0] }} - ${{ priceRange[1]
                                }}</span>
                        </div>
                        <!-- Placeholder for a more advanced slider component -->
                        <div class="w-full h-1 bg-gray-200 rounded-full relative">
                            <div class="absolute h-1 bg-primary rounded-full"
                                :style="{ left: leftHandle, right: rightHandle }"></div>
                            <div class="absolute w-6 h-6 -mt-2 -ml-3 rounded-full bg-primary shadow-lg cursor-pointer"
                                :style="{ left: leftHandle }" @mousedown="startDrag('min')"></div>
                            <div class="absolute w-6 h-6 -mt-2 -ml-3 rounded-full bg-primary shadow-lg cursor-pointer"
                                :style="{ left: rightHandle }" @mousedown="startDrag('max')"></div>
                        </div>
                    </div>
                </div>

                <!-- Action buttons -->
                <div class="bg-gray-50 px-4 py-3 flex space-x-4 border-t border-gray-200">
                    <button type="button"
                        class="flex-1 rounded-lg border border-gray-300 shadow-sm px-6 py-3 text-base font-semibold text-gray-700 bg-white hover:bg-gray-50"
                        @click="resetFilters">
                        RESET
                    </button>
                    <button type="button"
                        class="flex-1 rounded-lg border border-transparent shadow-sm px-6 py-3 text-base font-semibold text-white bg-primary hover:bg-primary/90"
                        @click="applyFilters">
                        APPLY
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
<style scoped>
.text-primary {
    color: rgb(var(--color-primary));
}

.bg-primary {
    background-color: rgb(var(--color-primary));
}

.text-text-main {
    color: rgb(var(--color-text-main));
}

.scrollbar-hide {
    -ms-overflow-style: none;
    /* IE and Edge */
    scrollbar-width: none;
    /* Firefox */
}

.scrollbar-hide::-webkit-scrollbar {
    display: none;
    /* Chrome, Safari, Opera */
}
</style>