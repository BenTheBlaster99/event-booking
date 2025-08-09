<template>
    <div class="max-w-[480px] mx-auto min-h-screen bg-gray-50 font-sans">
      <!-- Header -->
      <header class="p-4 bg-white shadow-sm flex items-center justify-between sticky top-0 z-10">
        <button @click="$router.go(-1)" class="text-xl">
          <font-awesome-icon :icon="['fas', 'arrow-left']" />
        </button>
        <h1 class="text-lg font-bold text-text-main">Create Event</h1>
        <button 
          @click="submitEvent" 
          :disabled="loading || !isFormValid"
          class="text-primary font-semibold disabled:text-gray-400"
        >
          {{ loading ? 'Saving...' : 'Save' }}
        </button>
      </header>
  
      <!-- Form Content -->
      <div class="p-4 space-y-6 pb-20">
        <!-- Image Upload Section -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
          <div class="relative">
            <div 
              v-if="!form.imagePreview"
              class="h-48 bg-gradient-to-br from-primary to-primary-dark flex items-center justify-center cursor-pointer"
              @click="$refs.imageInput.click()"
            >
              <div class="text-center text-white">
                <font-awesome-icon :icon="['fas', 'camera']" class="text-4xl mb-2" />
                <p class="font-semibold">Add Event Photo</p>
                <p class="text-sm opacity-80">Tap to upload image</p>
              </div>
            </div>
            <div v-else class="relative">
              <img :src="form.imagePreview" alt="Event preview" class="w-full h-48 object-cover">
              <button 
                @click="removeImage"
                class="absolute top-2 right-2 bg-black bg-opacity-50 text-white rounded-full w-8 h-8 flex items-center justify-center"
              >
                <font-awesome-icon :icon="['fas', 'times']" />
              </button>
              <button 
                @click="$refs.imageInput.click()"
                class="absolute bottom-2 right-2 bg-primary text-white rounded-lg px-3 py-1 text-sm font-semibold"
              >
                Change
              </button>
            </div>
            <input 
              ref="imageInput"
              type="file" 
              accept="image/*" 
              class="hidden" 
              @change="handleImageUpload"
            >
          </div>
        </div>
  
        <!-- Event Details Form -->
        <form @submit.prevent="submitEvent" class="space-y-6">
          <!-- Title -->
          <div class="bg-white rounded-xl shadow-sm p-4">
            <label class="block text-sm font-semibold text-gray-700 mb-2">Event Title *</label>
            <input 
              v-model="form.title"
              type="text" 
              placeholder="Enter event title"
              class="w-full px-0 py-2 text-lg font-semibold border-0 border-b border-gray-200 focus:outline-none focus:border-primary bg-transparent"
              required
            >
          </div>
  
          <!-- Description -->
          <div class="bg-white rounded-xl shadow-sm p-4">
            <label class="block text-sm font-semibold text-gray-700 mb-2">Description *</label>
            <textarea 
              v-model="form.description"
              placeholder="Describe your event..."
              rows="4"
              class="w-full px-0 py-2 border-0 border-b border-gray-200 focus:outline-none focus:border-primary bg-transparent resize-none"
              required
            ></textarea>
          </div>
  
          <!-- Category -->
          <div class="bg-white rounded-xl shadow-sm p-4">
            <label class="block text-sm font-semibold text-gray-700 mb-3">Category *</label>
            <div class="grid grid-cols-3 gap-3">
              <button 
                v-for="category in categories" 
                :key="category.id"
                type="button"
                @click="form.category_id = category.id"
                class="flex flex-col items-center p-3 rounded-lg border-2 transition-all"
                :class="{
                  'border-primary bg-primary bg-opacity-10': form.category_id === category.id,
                  'border-gray-200 hover:border-gray-300': form.category_id !== category.id
                }"
              >
                <span class="text-2xl mb-1">{{ getCategoryIcon(category.name) }}</span>
                <span class="text-xs font-medium text-center">{{ category.name }}</span>
              </button>
            </div>
          </div>
  
          <!-- Date & Time -->
          <div class="bg-white rounded-xl shadow-sm p-4 space-y-4">
            <h3 class="text-sm font-semibold text-gray-700">Date & Time *</h3>
            
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-xs text-gray-500 mb-1">Start Date</label>
                <input 
                  v-model="form.start_date"
                  type="date" 
                  class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"
                  required
                >
              </div>
              <div>
                <label class="block text-xs text-gray-500 mb-1">Start Time</label>
                <input 
                  v-model="form.start_time"
                  type="time" 
                  class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"
                  required
                >
              </div>
            </div>
  
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-xs text-gray-500 mb-1">End Date</label>
                <input 
                  v-model="form.end_date"
                  type="date" 
                  class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"
                  required
                >
              </div>
              <div>
                <label class="block text-xs text-gray-500 mb-1">End Time</label>
                <input 
                  v-model="form.end_time"
                  type="time" 
                  class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"
                  required
                >
              </div>
            </div>
          </div>
  
          <!-- Location -->
          <div class="bg-white rounded-xl shadow-sm p-4">
            <label class="block text-sm font-semibold text-gray-700 mb-2">Location *</label>
            <div class="relative mb-3">
              <font-awesome-icon :icon="['fas', 'location-dot']" class="absolute left-3 top-1/2 -translate-y-1/2 text-primary" />
              <input 
                v-model="form.location"
                type="text" 
                placeholder="Enter event location"
                class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"
                required
              >
            </div>
            
            <!-- Coordinates (Optional) -->
            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block text-xs text-gray-500 mb-1">Latitude (Optional)</label>
                <input 
                  v-model="form.latitude"
                  type="number" 
                  step="any"
                  placeholder="40.7128"
                  class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary text-sm"
                >
              </div>
              <div>
                <label class="block text-xs text-gray-500 mb-1">Longitude (Optional)</label>
                <input 
                  v-model="form.longitude"
                  type="number" 
                  step="any"
                  placeholder="-74.0060"
                  class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary text-sm"
                >
              </div>
            </div>
            
            <button 
              type="button"
              @click="getCurrentLocation"
              class="mt-3 w-full bg-gray-100 text-gray-700 py-2 rounded-lg font-medium hover:bg-gray-200 transition-colors flex items-center justify-center"
            >
              <font-awesome-icon :icon="['fas', 'crosshairs']" class="mr-2" />
              Use Current Location
            </button>
          </div>
  
          <!-- Submit Button -->
          <button 
            type="submit"
            :disabled="loading || !isFormValid"
            class="w-full bg-primary text-white py-4 rounded-xl font-bold text-lg shadow-lg hover:bg-primary-dark transition-colors disabled:bg-gray-300 disabled:cursor-not-allowed flex items-center justify-center"
          >
            <font-awesome-icon v-if="loading" :icon="['fas', 'spinner']" class="animate-spin mr-2" />
            {{ loading ? 'Creating Event...' : 'Create Event' }}
          </button>
        </form>
      </div>
    </div>
  </template>
  
  <script>
  import api from '@/api';
  import { library } from '@fortawesome/fontawesome-svg-core';
  import { 
    faArrowLeft, 
    faCamera, 
    faTimes, 
    faLocationDot, 
    faCrosshairs,
    faSpinner
  } from '@fortawesome/free-solid-svg-icons';
  import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
  
  library.add(faArrowLeft, faCamera, faTimes, faLocationDot, faCrosshairs, faSpinner);
  
  export default {
    name: 'CreateEventPage',
    components: {
      FontAwesomeIcon
    },
    data() {
      return {
        loading: false,
        categories: [],
        form: {
          title: '',
          description: '',
          start_date: '',
          start_time: '',
          end_date: '',
          end_time: '',
          location: '',
          latitude: '',
          longitude: '',
          category_id: null,
          image: null,
          imagePreview: null
        }
      };
    },
    computed: {
      isFormValid() {
        return this.form.title && 
               this.form.description && 
               this.form.start_date && 
               this.form.start_time && 
               this.form.end_date && 
               this.form.end_time && 
               this.form.location && 
               this.form.category_id;
      }
    },
    async created() {
      await this.fetchCategories();
      this.setDefaultDates();
    },
    methods: {
      async fetchCategories() {
        try {
          const response = await api.get('/categories');
          if (response.data.success) {
            this.categories = response.data.data;
          }
        } catch (error) {
          console.error('Failed to fetch categories:', error);
        }
      },
  
      setDefaultDates() {
        const now = new Date();
        const tomorrow = new Date(now);
        tomorrow.setDate(tomorrow.getDate() + 1);
        
        this.form.start_date = tomorrow.toISOString().split('T')[0];
        this.form.end_date = tomorrow.toISOString().split('T')[0];
        this.form.start_time = '18:00';
        this.form.end_time = '21:00';
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
  
      handleImageUpload(event) {
        const file = event.target.files[0];
        if (!file) return;
  
        // Validate file size (max 5MB)
        if (file.size > 5 * 1024 * 1024) {
          alert('Image size must be less than 5MB');
          return;
        }
  
        // Validate file type
        if (!file.type.startsWith('image/')) {
          alert('Please select a valid image file');
          return;
        }
  
        this.form.image = file;
        
        // Create preview
        const reader = new FileReader();
        reader.onload = (e) => {
          this.form.imagePreview = e.target.result;
        };
        reader.readAsDataURL(file);
      },
  
      removeImage() {
        this.form.image = null;
        this.form.imagePreview = null;
        this.$refs.imageInput.value = '';
      },
  
      getCurrentLocation() {
        if (!navigator.geolocation) {
          alert('Geolocation is not supported by this browser');
          return;
        }
  
        navigator.geolocation.getCurrentPosition(
          (position) => {
            this.form.latitude = position.coords.latitude.toFixed(6);
            this.form.longitude = position.coords.longitude.toFixed(6);
          },
          (error) => {
            console.error('Error getting location:', error);
            alert('Unable to get your location. Please enter coordinates manually.');
          }
        );
      },
  
      async submitEvent() {
        if (!this.isFormValid) return;
  
        this.loading = true;
        try {
          const formData = new FormData();
          
          // Combine date and time
          const startDateTime = `${this.form.start_date} ${this.form.start_time}:00`;
          const endDateTime = `${this.form.end_date} ${this.form.end_time}:00`;
          
          formData.append('title', this.form.title);
          formData.append('description', this.form.description);
          formData.append('start_time', startDateTime);
          formData.append('end_time', endDateTime);
          formData.append('location', this.form.location);
          formData.append('category_id', this.form.category_id);
          
          if (this.form.latitude) {
            formData.append('latitude', this.form.latitude);
          }
          if (this.form.longitude) {
            formData.append('longitude', this.form.longitude);
          }
          if (this.form.image) {
            formData.append('image', this.form.image);
          }
  
          const response = await api.post('/events', formData, {
            headers: {
              'Content-Type': 'multipart/form-data'
            }
          });
  
          if (response.data.success) {
            alert('Event created successfully!');
            this.$router.push('/home/events');
          } else {
            throw new Error(response.data.message || 'Failed to create event');
          }
        } catch (error) {
          console.error('Error creating event:', error);
          alert(error.response?.data?.message || 'Failed to create event. Please try again.');
        } finally {
          this.loading = false;
        }
      }
    }
  };
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
  
  .hover\:bg-primary-dark:hover {
    background-color: rgb(var(--color-primary-dark));
  }
  
  /* Custom scrollbar for mobile */
  ::-webkit-scrollbar {
    display: none;
  }
  
  /* Focus styles */
  input:focus, textarea:focus {
    transform: translateY(-1px);
  }
  </style>