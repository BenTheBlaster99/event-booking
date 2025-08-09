<template>
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"
                @click="$emit('close')"></div>

            <!-- Modal panel -->
            <div
                class="inline-block align-bottom bg-white rounded-t-3xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle w-full max-w-md mx-auto">
                <div class="p-6">
                    <!-- Drag handle -->
                    <div class="flex justify-center mb-4">
                        <div class="w-12 h-1 bg-gray-300 rounded-full"></div>
                    </div>

                    <h3 class="text-xl leading-6 font-bold text-text-main mb-6" id="modal-title">Filter</h3>

                    <!-- Categories Section -->
                    <div class="mb-6">
                        <div class="flex overflow-x-auto space-x-4 pb-2 scrollbar-hide">
                            <button v-for="category in categories" :key="category.name"
                                class="flex-shrink-0 flex flex-col items-center p-3 rounded-2xl transition-colors duration-200 min-w-[70px]"
                                :class="{
                                    'bg-primary text-white': selectedCategories.includes(category.name),
                                    'bg-gray-100 text-gray-700': !selectedCategories.includes(category.name)
                                }" @click="toggleCategory(category.name)">
                                <div class="w-12 h-12 flex items-center justify-center rounded-full mb-2"
                                    :class="{ 
                                        'bg-white bg-opacity-20': selectedCategories.includes(category.name), 
                                        'bg-white': !selectedCategories.includes(category.name) 
                                    }">
                                    <span class="text-2xl">{{ category.icon }}</span>
                                </div>
                                <span class="text-xs font-medium text-center">{{ category.name }}</span>
                            </button>
                        </div>
                    </div>

                    <!-- Time & Date Section -->
                    <div class="mb-6">
                        <h4 class="text-lg font-semibold text-text-main mb-4">Time & Date</h4>
                        <div class="flex space-x-2 mb-4">
                            <button v-for="time in timeOptions" :key="time"
                                class="flex-1 py-2 px-4 rounded-lg font-medium transition-colors duration-200" :class="{
                                    'bg-primary text-white': selectedTime === time,
                                    'bg-gray-100 text-gray-700': selectedTime !== time
                                }" @click="selectedTime = selectedTime === time ? '' : time">
                                {{ time }}
                            </button>
                        </div>
                        <button
                            class="w-full flex items-center justify-between py-3 px-4 rounded-lg bg-gray-100 text-gray-700 font-medium hover:bg-gray-200 transition-colors"
                            @click="showCalendar = !showCalendar">
                            <div class="flex items-center">
                                <font-awesome-icon :icon="['fas', 'calendar-alt']" class="mr-3 text-primary" />
                                <span>Choose from calendar</span>
                            </div>
                            <font-awesome-icon :icon="['fas', 'chevron-right']" class="text-gray-400" />
                        </button>
                    </div>

                    <!-- Location Section -->
                    <div class="mb-6">
                        <h4 class="text-lg font-semibold text-text-main mb-4">Location</h4>
                        <div class="relative">
                            <font-awesome-icon :icon="['fas', 'location-dot']"
                                class="absolute left-4 top-1/2 -translate-y-1/2 text-primary" />
                            <input type="text" 
                                v-model="location"
                                placeholder="New York, USA"
                                class="w-full pl-12 pr-12 py-3 rounded-lg bg-gray-100 text-gray-700 font-medium focus:outline-none focus:ring-2 focus:ring-primary focus:bg-white transition-colors">
                            <font-awesome-icon :icon="['fas', 'chevron-right']"
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400" />
                        </div>
                    </div>

                    <!-- Price Range Section -->
                    <div class="mb-8">
                        <div class="flex justify-between items-center mb-4">
                            <h4 class="text-lg font-semibold text-text-main">Select price range</h4>
                            <span class="font-semibold text-primary">${{ priceRange[0] }}-${{ priceRange[1] }}</span>
                        </div>
                        
                        <!-- Custom Range Slider -->
                        <div class="relative">
                            <div class="w-full h-2 bg-gray-200 rounded-full relative">
                                <!-- Active range -->
                                <div class="absolute h-2 bg-primary rounded-full" 
                                     :style="{ 
                                         left: leftPercentage + '%', 
                                         width: (rightPercentage - leftPercentage) + '%' 
                                     }"></div>
                                
                                <!-- Min handle -->
                                <div class="absolute w-6 h-6 -mt-2 -ml-3 rounded-full bg-primary shadow-lg cursor-pointer border-2 border-white"
                                     :style="{ left: leftPercentage + '%' }" 
                                     @mousedown="startDrag('min')"
                                     @touchstart="startDrag('min')"></div>
                                
                                <!-- Max handle -->
                                <div class="absolute w-6 h-6 -mt-2 -ml-3 rounded-full bg-primary shadow-lg cursor-pointer border-2 border-white"
                                     :style="{ left: rightPercentage + '%' }" 
                                     @mousedown="startDrag('max')"
                                     @touchstart="startDrag('max')"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action buttons -->
                <div class="bg-gray-50 px-6 py-4 flex space-x-4">
                    <button type="button"
                        class="flex-1 rounded-lg border border-gray-300 shadow-sm px-6 py-3 text-base font-semibold text-gray-700 bg-white hover:bg-gray-50 transition-colors"
                        @click="resetFilters">
                        RESET
                    </button>
                    <button type="button"
                        class="flex-1 rounded-lg border border-transparent shadow-sm px-6 py-3 text-base font-semibold text-white bg-primary hover:bg-primary-dark transition-colors"
                        @click="applyFilters">
                        APPLY
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import api from '@/api';
import { library } from '@fortawesome/fontawesome-svg-core';
import { faCalendarAlt, faChevronRight, faLocationDot } from '@fortawesome/free-solid-svg-icons';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';

library.add(faCalendarAlt, faChevronRight, faLocationDot);

export default {
    name: 'FilterModal',
    components: {
        FontAwesomeIcon
    },
    props: {
        currentFilters: {
            type: Object,
            default: () => ({
                categories: [],
                time: '',
                location: '',
                priceRange: [20, 120]
            })
        }
    },
    data() {
        return {
            categories: [],
            selectedCategories: [...this.currentFilters.categories],
            timeOptions: ['Today', 'Tomorrow', 'This week'],
            selectedTime: this.currentFilters.time,
            location: this.currentFilters.location || 'New York, USA',
            minPrice: 20,
            maxPrice: 120,
            priceRange: [...this.currentFilters.priceRange],
            isDragging: null,
            showCalendar: false,
        }
    },
    computed: {
        leftPercentage() {
            return ((this.priceRange[0] - this.minPrice) / (this.maxPrice - this.minPrice)) * 100;
        },
        rightPercentage() {
            return ((this.priceRange[1] - this.minPrice) / (this.maxPrice - this.minPrice)) * 100;
        }
    },
    async created() {
        await this.fetchCategories();
    },
    beforeUnmount() {
        this.stopDrag();
    },
    methods: {
        async fetchCategories() {
            try {
                const response = await api.get('/categories');
                if (response.data.success) {
                    this.categories = response.data.data.map(cat => ({
                        name: cat.name,
                        icon: this.getCategoryIcon(cat.name)
                    }));
                }
            } catch (error) {
                console.error('Failed to fetch categories:', error);
                // Fallback to default categories
                this.categories = [
                    { name: 'Sports', icon: '⚽' },
                    { name: 'Music', icon: '🎵' },
                    { name: 'Art', icon: '🎨' },
                    { name: 'Food', icon: '🍔' },
                    { name: 'Business', icon: '💼' },
                    { name: 'Tech', icon: '💻' }
                ];
            }
        },

        getCategoryIcon(categoryName) {
            const icons = {
                'Sports': '⚽',
                'Music': '🎵',
                'Art': '🎨',
                'Food': '🍔',
                'Business': '💼',
                'Tech': '💻',
                'Technology': '💻',
                'default': '📅'
            };
            return icons[categoryName] || icons.default;
        },

        toggleCategory(categoryName) {
            const index = this.selectedCategories.indexOf(categoryName);
            if (index > -1) {
                this.selectedCategories.splice(index, 1);
            } else {
                this.selectedCategories.push(categoryName);
            }
        },

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
            document.addEventListener('mousemove', this.onDrag);
            document.addEventListener('mouseup', this.stopDrag);
            document.addEventListener('touchmove', this.onDrag);
            document.addEventListener('touchend', this.stopDrag);
        },

        onDrag(e) {
            if (!this.isDragging) return;
            
            const slider = this.$el.querySelector('.w-full.h-2.bg-gray-200');
            if (!slider) return;

            const rect = slider.getBoundingClientRect();
            const clientX = e.touches ? e.touches[0].clientX : e.clientX;
            let newPosition = (clientX - rect.left) / rect.width;

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
            document.removeEventListener('mousemove', this.onDrag);
            document.removeEventListener('mouseup', this.stopDrag);
            document.removeEventListener('touchmove', this.onDrag);
            document.removeEventListener('touchend', this.stopDrag);
        }
    }
}
</script>

<style scoped>
.text-primary {
    color: rgb(var(--color-primary));
}

.bg-primary {
    background-color: rgb(var(--color-primary));
}

.bg-primary-dark {
    background-color: rgb(var(--color-primary-dark));
}

.text-text-main {
    color: rgb(var(--color-text-main));
}

.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}

.scrollbar-hide::-webkit-scrollbar {
    display: none;
}

/* Prevent text selection during drag */
.cursor-pointer {
    user-select: none;
}
</style>