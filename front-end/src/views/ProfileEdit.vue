<script>
    import api from '@/api';
    export default {
        data() {
            return {
                user: {},
                form: {
                    name:'',
                    emai:'',
                    bio:''
                },
                avatarFile: null,
                loading:false
            };
        },
        async created(){
            await this.fetchProfile();   
        },
        methods:{
            async fetchProfile(){
                const response = await api.get('/profile');
                this.user= response.data.data.data;
                this.form = {
                    name: this.user.name,
                    email:this.user.email,
                    bio:this.user.bio || ''
                };
            },
            async handleAvatarChange(e){
                const file = e.target.files[0];
                if(!file) return;
                this.avatarFile = file;
                const reader = new FileReader();
                reader.onload= (e)=>{
                    this.user.avatar = e.target.result;
                };
                reader.readAsDataURL(file);
            },
            async updateProfile(){
                this.loading = true;
                try{
                    if(this.avatarFile){
                        const formData = new FormData();
                        formData.append('avatar', this.avatarFile);
                        await api.post('/profile/avatar', formData, {
                            headers:{
                                'Content-Type': 'multipart/form-data'
                            }
                        });
                    }
                    await api.put('/profile',this.form);
                    this.$router.push('/home/profile');
                }catch (error){
                    console.error('Error updating profile:', error);
                    
                } finally {
                    this.loading = false ;
                }
            }
        }
    }
</script>
<template>
    <div class="max-w-[480px] mx-auto min-h-screen bg-gray-50 font-sans">
      <header class="p-4 flex items-center border-b border-gray-200">
        <button @click="$router.go(-1)" class="text-xl">
          <font-awesome-icon :icon="['fas', 'arrow-left']" />
        </button>
        <h1 class="text-lg font-bold ml-4">Edit Profile</h1>
      </header>
  
      <div class="p-4">
        <!-- Avatar Upload -->
        <div class="flex flex-col items-center mb-6">
          <img :src="user.avatar" alt="Profile" class="w-24 h-24 rounded-full object-cover mb-4">
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
            class="bg-primary text-white px-4 py-2 rounded-lg"
          >
            Change Avatar
          </button>
        </div>
  
        <!-- Profile Form -->
        <form @submit.prevent="updateProfile" class="space-y-4">
          <div class="space-y-2">
            <label class="block text-sm font-medium text-gray-700">Name</label>
            <input 
              v-model="form.name" 
              type="text" 
              class="w-full px-4 py-2 border border-gray-300 rounded-lg"
            >
          </div>
  
          <div class="space-y-2">
            <label class="block text-sm font-medium text-gray-700">Email</label>
            <input 
              v-model="form.email" 
              type="email" 
              disabled
              class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100"
            >
          </div>
  
          <div class="space-y-2">
            <label class="block text-sm font-medium text-gray-700">Bio</label>
            <textarea 
              v-model="form.bio" 
              class="w-full px-4 py-2 border border-gray-300 rounded-lg"
              rows="3"
            ></textarea>
          </div>
  
          <button 
            type="submit" 
            class="w-full bg-primary text-white py-3 rounded-lg font-semibold"
            :disabled="loading"
          >
            {{ loading ? 'Saving...' : 'Save Changes' }}
          </button>
        </form>
      </div>
    </div>
  </template>
  