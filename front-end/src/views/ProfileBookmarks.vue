<template>
    <div class="max-w-[480px] mx-auto min-h-screen bg-gray-50 font-sans">
      <header class="p-4 flex items-center bg-white shadow-sm">
        <button @click="$router.go(-1)" class="text-xl mr-4">
          <font-awesome-icon :icon="['fas', 'arrow-left']" />
        </button>
        <h1 class="text-lg font-bold">Edit Profile</h1>
      </header>
  
      <div v-if="loading && !user.name" class="p-4 text-center">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary mx-auto mb-2"></div>
        <p class="text-gray-600">Loading profile...</p>
      </div>

      <div v-else class="p-4">
        <!-- Avatar Upload -->
        <div class="flex flex-col items-center mb-6 bg-white rounded-lg p-6 shadow-sm">
          <img 
            :src="user.avatar || 'https://placehold.co/80x80/E0F2F7/2C3E50?text=P'" 
            alt="Profile" 
            class="w-24 h-24 rounded-full object-cover mb-4 border-4 border-gray-100"
          >
          <input 
            type="file" 
            id="avatar" 
            ref="avatarInput" 
            accept="image/*" 
            class="hidden" 
            @change="handleAvatarChange"
          >
          <button 
            @click="$refs.avatarInput.click()"
            class="bg-primary text-white px-6 py-2 rounded-lg font-semibold hover:bg-primary-dark transition-colors"
          >
            <font-awesome-icon :icon="['fas', 'camera']" class="mr-2" />
            Change Avatar
          </button>
        </div>
  
        <!-- Profile Form -->
        <form @submit.prevent="updateProfile" class="space-y-4">
          <div class="bg-white rounded-lg p-4 shadow-sm">
            <div class="space-y-4">
              <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-700">Name</label>
                <input 
                  v-model="form.name" 
                  type="text" 
                  required
                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                  placeholder="Enter your name"
                >
              </div>
    
              <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-700">Email</label>
                <input 
                  v-model="form.email" 
                  type="email" 
                  disabled
                  class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-100 text-gray-500"
                  placeholder="Email cannot be changed"
                >
              </div>
    
              <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-700">Bio</label>
                <textarea 
                  v-model="form.bio" 
                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                  rows="3"
                  placeholder="Tell us about yourself..."
                ></textarea>
              </div>
            </div>
          </div>
  
          <button 
            type="submit" 
            class="w-full bg-primary text-white py-3 rounded-lg font-semibold hover:bg-primary-dark transition-colors flex items-center justify-center"
            :disabled="loading"
          >
            <font-awesome-icon v-if="loading" :icon="['fas', 'spinner']" class="animate-spin mr-2" />
            {{ loading ? 'Saving...' : 'Save Changes' }}
          </button>
        </form>
      </div>
    </div>
  </template>
  
  <script>
  import api from '@/api';
  import { library } from '@fortawesome/fontawesome-svg-core';
  import { faArrowLeft, faCamera, faSpinner } from '@fortawesome/free-solid-svg-icons';
  import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';

  library.add(faArrowLeft, faCamera, faSpinner);

  export default {
      components: {
          FontAwesomeIcon
      },
      data() {
          return {
              user: {},
              form: {
                  name: '',
                  email: '',
                  bio: ''
              },
              avatarFile: null,
              loading: false
          };
      },
      async created() {
          await this.fetchProfile();   
      },
      methods: {
          async fetchProfile() {
              this.loading = true;
              try {
                  const response = await api.get('/profile');
                  console.log('Profile response:', response.data);
                  
                  if (response.data.success) {
                      this.user = {
                          ...response.data.data,
                          avatar: response.data.data.avatar 
                              ? `http://localhost:8000/storage/${response.data.data.avatar}` 
                              : 'https://placehold.co/80x80/E0F2F7/2C3E50?text=P'
                      };
                      this.form = {
                          name: this.user.name || '',
                          email: this.user.email || '',
                          bio: this.user.bio || ''
                      };
                  }
              } catch (error) {
                  console.error('Failed to fetch profile:', error);
              } finally {
                  this.loading = false;
              }
          },

          async handleAvatarChange(e) {
              const file = e.target.files[0];
              if (!file) return;
              
              // Validate file size (max 5MB)
              if (file.size > 5 * 1024 * 1024) {
                  alert('File size must be less than 5MB');
                  return;
              }
              
              this.avatarFile = file;
              const reader = new FileReader();
              reader.onload = (e) => {
                  this.user.avatar = e.target.result;
              };
              reader.readAsDataURL(file);
          },

          async updateProfile() {
              this.loading = true;
              try {
                  // Upload avatar if changed
                  if (this.avatarFile) {
                      const formData = new FormData();
                      formData.append('avatar', this.avatarFile);
                      await api.post('/profile/avatar', formData, {
                          headers: {
                              'Content-Type': 'multipart/form-data'
                          }
                      });
                  }
                  
                  // Update profile info
                  await api.put('/profile', this.form);
                  
                  // Success feedback
                  alert('Profile updated successfully!');
                  this.$router.push('/home/profile');
              } catch (error) {
                  console.error('Error updating profile:', error);
                  alert('Failed to update profile. Please try again.');
              } finally {
                  this.loading = false;
              }
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

  .hover\:bg-primary-dark:hover {
      background-color: rgb(var(--color-primary-dark));
  }
  </style>